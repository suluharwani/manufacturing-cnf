<?php

namespace App\Controllers;

use App\Models\MdlEmployee;
use App\Models\MasterPenggajianDetailModel;
use App\Models\MasterPenggajianModel;
use App\Models\MdlFemployeeAllowanceList;
use App\Models\MdlFemployeeDeductionList;
use App\Models\MdlEffectiveHours;
use App\Models\MdlSalaryCat;
use App\Models\AttendanceModel;
use App\Controllers\BaseController;

class MasterPenggajianDetailController extends BaseController
{
    public function dataEmployeeMaster($penggajianId = null)
    {
        if ($penggajianId === null) {
            return redirect()->back()->with('error', 'Penggajian ID tidak ditemukan.');
        }

        // Inisialisasi model
        $penggajianDetailModel = new MasterPenggajianDetailModel();
        $allowanceModel = new MdlFemployeeAllowanceList();
        $deductionModel = new MdlFemployeeDeductionList();
        $effectiveHoursModel = new MdlEffectiveHours();
        $attendanceModel = new AttendanceModel();
        $salaryCatModel = new MdlSalaryCat();
        // $patternMdl = new MdlFsalaryPatternEmployee();
        // Dapatkan data penggajian dengan join tabel pegawai dan master_penggajian
        $results = $penggajianDetailModel
            ->select('salary_pattern_employee.id_salary_pattern as pattern_id, master_penggajian_detail.karyawan_id, pegawai.pegawai_nama, pegawai.pegawai_pin, master_penggajian.kode_penggajian, master_penggajian.tanggal_awal_penggajian, master_penggajian.tanggal_akhir_penggajian')
            ->join('master_penggajian', 'master_penggajian_detail.penggajian_id = master_penggajian.id', 'left')
            ->join('pegawai', 'master_penggajian_detail.karyawan_id = pegawai.pegawai_id', 'left')
            ->join('salary_pattern_employee', 'salary_pattern_employee.id_employee = pegawai.pegawai_id', 'left')
            ->where('master_penggajian_detail.penggajian_id', $penggajianId)
            ->orderBy('master_penggajian_detail.karyawan_id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($results as &$result) {
            $employeeId = $result['karyawan_id'];
            $pin = $result['pegawai_pin'];
            $startDate = $result['tanggal_awal_penggajian'];
            $endDate = $result['tanggal_akhir_penggajian'];

            // Hitung total allowance dan deduction
            $totalAllowance = $this->calculateTotalAllowance($allowanceModel, $employeeId);
            $totalDeduction = $this->calculateTotalDeduction($deductionModel, $employeeId);

            // Dapatkan data tarif gaji
            $idCatSal = $result['pattern_id'];

            $salaryRate = $salaryCatModel->where('id', $idCatSal)->first();

            // Mendapatkan data kehadiran dan memproses in_time dan out_time
            $attendanceRecords = $attendanceModel->getAttendance($pin, $employeeId, $startDate, $endDate);
            $processedAttendance = $this->getInOutTimes($attendanceRecords);

            // Hitung total jam kerja dan lembur
            $workData = $this->calculateWorkAndOvertime($processedAttendance, $effectiveHoursModel);

            // Hitung total gaji
            $result['total_salary'] = $this->calculateSalary($employeeId,$workData, $salaryRate, $totalAllowance, $totalDeduction)['totalSalary'];
            $result['total_work_Hours'] = $workData['totalWorkHours'];
            $result['total_overtime1_Hours'] = $workData['totalOvertime1Hours'];
            $result['total_overtime2_Hours'] = $workData['totalOvertime2Hours'];
            $result['total_overtime3_Hours'] = $workData['totalOvertime3Hours'];
            $result['sunday_work_Hours'] = $workData['sundayWorkHours'];
        }

        // Mengembalikan hasil dalam format JSON
        return $this->response->setJSON($results);
    }

    // Fungsi untuk menghitung total allowance
    private function calculateTotalAllowance($allowanceModel, $employeeId)
    {
        return $allowanceModel
            ->selectSum('amount')
            ->where('employee_id', $employeeId)
            ->get()
            ->getRow()
            ->amount ?? 0;
    }

    // Fungsi untuk menghitung total deduction
    private function calculateTotalDeduction($deductionModel, $employeeId)
    {
        return $deductionModel
            ->selectSum('amount')
            ->where('employee_id', $employeeId)
            ->get()
            ->getRow()
            ->amount ?? 0;
    }

    // Fungsi untuk mendapatkan in_time dan out_time
    private function getInOutTimes($attendanceRecords)
    {
        $groupedData = [];

        foreach ($attendanceRecords as $record) {
            $pin = $record['pin'];
            $date = date('Y-m-d', strtotime($record['scan_date']));

            if (!isset($groupedData[$pin][$date])) {
                $groupedData[$pin][$date] = [
                    'in_time' => $record['scan_date'],
                    'out_time' => $record['scan_date'],
                ];
            } else {
                if ($record['scan_date'] < $groupedData[$pin][$date]['in_time']) {
                    $groupedData[$pin][$date]['in_time'] = $record['scan_date'];
                }
                if ($record['scan_date'] > $groupedData[$pin][$date]['out_time']) {
                    $groupedData[$pin][$date]['out_time'] = $record['scan_date'];
                }
            }
        }

        $result = [];
        foreach ($groupedData as $pin => $dates) {
            foreach ($dates as $date => $times) {
                $result[] = [
                    'pin' => $pin,
                    'date' => $date,
                    'in_time' => $times['in_time'],
                    'out_time' => $times['out_time'],
                ];
            }
        }

        return $result;
    }

    // Fungsi untuk menghitung durasi kerja dan lembur
private function calculateWorkMinutes($inTime, $outTime, $workDaySetting, $date)
{
    $normalWorkMinutes = 0;
    $overtimeMinutes1 = 0;
    $overtimeMinutes2 = 0;
    $overtimeMinutes3 = 0;

    if ($workDaySetting) {
        $workStart = new \DateTime("$date {$workDaySetting['work_start']}");
        $workEnd = new \DateTime("$date {$workDaySetting['work_end']}");
        $overtimeStart1 = new \DateTime("$date {$workDaySetting['overtime_start_1']}");
        $overtimeEnd1 = new \DateTime("$date {$workDaySetting['overtime_end_1']}");
        $overtimeStart2 = new \DateTime("$date {$workDaySetting['overtime_start_2']}");
        $overtimeEnd2 = new \DateTime("$date {$workDaySetting['overtime_end_2']}");
        $overtimeStart3 = new \DateTime("$date {$workDaySetting['overtime_start_3']}");
        $overtimeEnd3 = new \DateTime("$date {$workDaySetting['overtime_end_3']}");
        $workBreakStart = new \DateTime("$date {$workDaySetting['work_break']}");
        $workBreakEnd = new \DateTime("$date {$workDaySetting['work_break_end']}");

        // Adjust inTime if earlier than workStart
        if ($inTime < $workStart) {
            $inTime = $workStart;
        }

        // Calculate normal work minutes based on break times and workEnd
        if ($outTime < $workBreakStart) {
            $normalWorkMinutes = ($outTime->getTimestamp() - $inTime->getTimestamp()) / 60;
        } elseif ($outTime >= $workBreakStart && $outTime <= $workBreakEnd) {
            $normalWorkMinutes = ($workBreakStart->getTimestamp() - $inTime->getTimestamp()) / 60;
        } elseif ($outTime > $workEnd) {
            $normalWorkMinutes = ($workEnd->getTimestamp() - $inTime->getTimestamp()) / 60;
        } else {
            $normalWorkMinutes = ($outTime->getTimestamp() - $inTime->getTimestamp()) / 60;
        }

        // Adjust for break duration if outTime is after workBreakEnd
        if ($inTime < $workBreakEnd && $outTime > $workBreakEnd) {
            $breakStartTime = $inTime < $workBreakStart ? $workBreakStart : $inTime;
            $breakDuration = ($workBreakEnd->getTimestamp() - $breakStartTime->getTimestamp()) / 60;
            $normalWorkMinutes -= $breakDuration;
        }

        // Calculate overtime minutes if outTime is beyond specific overtime periods
        if ($outTime > $overtimeStart1) {
            $overtimeMinutes1 = min($overtimeEnd1->getTimestamp(), $outTime->getTimestamp()) - $overtimeStart1->getTimestamp();
            $overtimeMinutes1 = max($overtimeMinutes1 / 60, 0);
        }
        if ($outTime > $overtimeStart2) {
            $overtimeMinutes2 = min($overtimeEnd2->getTimestamp(), $outTime->getTimestamp()) - $overtimeStart2->getTimestamp();
            $overtimeMinutes2 = max($overtimeMinutes2 / 60, 0);
        }
        if ($outTime > $overtimeStart3) {
            $overtimeMinutes3 = min($overtimeEnd3->getTimestamp(), $outTime->getTimestamp()) - $overtimeStart3->getTimestamp();
            $overtimeMinutes3 = max($overtimeMinutes3 / 60, 0);
        }

        // Ensure all calculated minutes are non-negative
        $normalWorkMinutes = max($normalWorkMinutes, 0);
        $overtimeMinutes1 = max($overtimeMinutes1, 0);
        $overtimeMinutes2 = max($overtimeMinutes2, 0);
        $overtimeMinutes3 = max($overtimeMinutes3, 0);
    }

    return [
        'normalWorkMinutes' => $normalWorkMinutes,
        'overtimeMinutes1' => $overtimeMinutes1,
        'overtimeMinutes2' => $overtimeMinutes2,
        'overtimeMinutes3' => $overtimeMinutes3,
    ];
}



    private function getDayInIndonesian($date)
{
    $dayOfWeek = date('N', strtotime($date)); // Menggunakan format angka untuk hari (1 = Senin, 7 = Minggu)
    $daysInIndonesian = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu'
    ];

    return $daysInIndonesian[$dayOfWeek];
}
private function calculateWorkAndOvertime($processedAttendance, $effectiveHoursModel)
{
    $totalWorkMinutes = 0;
    $totalOvertime1Minutes = 0;
    $totalOvertime2Minutes = 0;
    $totalOvertime3Minutes = 0;
    $totalsundayWorkMinutesOT1  =0;
    $totalsundayWorkMinutesOT2  =0;
    $totalsundayWorkMinutesOT3  =0;
    $sundayWorkMinutes = 0;

    foreach ($processedAttendance as $attendance) {
        $date = $attendance['date'];
        $dayOfWeek = $this->getDayInIndonesian($date);
        $workDaySetting = $effectiveHoursModel->where('day', $dayOfWeek)->first();

        if (!$workDaySetting) {
            continue; // Jika tidak ada pengaturan hari kerja, lewati iterasi ini
        }

        // Mendapatkan waktu masuk dan keluar
        $inTime = new \DateTime($attendance['in_time']);
        $outTime = new \DateTime($attendance['out_time']);
        $duration = ($outTime->getTimestamp() - $inTime->getTimestamp()) / 60; // Durasi dalam menit

        // Hitung durasi kerja dan lembur
        $workData = $this->calculateWorkMinutes($inTime, $outTime, $workDaySetting, $date);

        // Tambahkan hasil perhitungan ke total
        if ($dayOfWeek !== 'Minggu') {
        $totalWorkMinutes += $workData['normalWorkMinutes'];

        // Set overtimeMinutes1 to 0 if it's less than 75 minutes
        $overtimeMinutes1 = $workData['overtimeMinutes1'] >= 75 ? $workData['overtimeMinutes1'] : 0;
        $totalOvertime1Minutes += $overtimeMinutes1;

        // Bulatkan lembur level 2 dan 3 ke bawah per 15 menit
        $roundedOvertime2 = floor($workData['overtimeMinutes2'] / 15) * 15;
        $roundedOvertime3 = floor($workData['overtimeMinutes3'] / 15) * 15;

        $totalOvertime2Minutes += $roundedOvertime2;
        $totalOvertime3Minutes += $roundedOvertime3;


        }


        // Jika hari adalah Minggu, tambahkan ke total jam kerja Minggu
        if ($dayOfWeek === 'Minggu') {
        $sundayWorkMinutes += $workData['normalWorkMinutes'];

        // Set overtimeMinutes1 to 0 if it's less than 75 minutes
        $sundayWorkMinutesOT1 = $workData['overtimeMinutes1'] >= 75 ? $workData['overtimeMinutes1'] : 0;
        $totalsundayWorkMinutesOT1 += $sundayWorkMinutesOT1;

        // Bulatkan lembur level 2 dan 3 ke bawah per 15 menit
        $sundayWorkMinutesOT2 = floor($workData['overtimeMinutes2'] / 15) * 15;
        $sundayWorkMinutesOT3 = floor($workData['overtimeMinutes3'] / 15) * 15;

        $totalsundayWorkMinutesOT2 += $sundayWorkMinutesOT2;
        $totalsundayWorkMinutesOT3 += $sundayWorkMinutesOT3 = floor($workData['overtimeMinutes3'] / 15) * 15;
;

        $sundayWorkMinutes +=$totalsundayWorkMinutesOT1+$sundayWorkMinutesOT2+$totalsundayWorkMinutesOT3;

            // $sundayWorkMinutes += $duration;
        }
    }

    // Konversi menit menjadi jam dengan 2 desimal
    $totalWorkHours = round($totalWorkMinutes / 60, 2);
    $totalOvertime1Hours = round($totalOvertime1Minutes / 60, 2);
    $totalOvertime2Hours = round($totalOvertime2Minutes / 60, 2);
    $totalOvertime3Hours = round($totalOvertime3Minutes / 60, 2);
    $sundayWorkHours = round($sundayWorkMinutes / 60, 2);

    return [
        'totalWorkHours' => $totalWorkHours,
        'totalOvertime1Hours' => $totalOvertime1Hours,
        'totalOvertime2Hours' => $totalOvertime2Hours,
        'totalOvertime3Hours' => $totalOvertime3Hours,
        'sundayWorkHours' => $sundayWorkHours,
    ];
}


private function calculateSalary($employeeId,$workData, $salaryRate, $totalAllowance, $totalDeduction)
{
    if (isset($salaryRate) && isset($salaryRate['Gaji_Per_Jam'])) {
        $totalNormalSalary = $workData['totalWorkHours'] * $salaryRate['Gaji_Per_Jam'];
          $totalOvertime1Hours =  $workData['totalOvertime1Hours'];
    $totalOvertime1Salary = $totalOvertime1Hours * $salaryRate['Gaji_Per_Jam'];

    // Membulatkan lembur level 2 dan 3 ke kelipatan 15 menit (0.25 jam)
    $totalOvertime2HoursRounded = ceil($workData['totalOvertime2Hours'] / 0.25) * 0.25;
    $totalOvertime3HoursRounded = ceil($workData['totalOvertime3Hours'] / 0.25) * 0.25;

    // Hitung gaji lembur level 2 dan 3 dengan waktu yang sudah dibulatkan
    $totalOvertime2Salary = $totalOvertime2HoursRounded * $salaryRate['Gaji_Per_Jam'];
    $totalOvertime3Salary = $totalOvertime3HoursRounded * $salaryRate['Gaji_Per_Jam'];

    // Hitung total gaji sebelum tunjangan dan potongan
    $grossSalary = $totalNormalSalary + $totalOvertime1Salary + $totalOvertime2Salary + $totalOvertime3Salary;

    // Hitung gaji total setelah menambahkan tunjangan dan mengurangi potongan
    $gajiMinggu = $workData['sundayWorkHours'] * $salaryRate['Gaji_Per_Jam_Hari_Minggu'];
    $totalSalary = ( $gajiMinggu+$grossSalary + $totalAllowance) - $totalDeduction;

} else {
    $totalNormalSalary = 0;
    $totalNormalSalary= 0;
    $totalOvertime1Salary= 0;
    $totalOvertime2Salary= 0;
    $totalOvertime3Salary= 0;
    $grossSalary= 0;
    $totalAllowance= 0;
    $totalAllowance= 0;
    $totalDeduction= 0;
    $totalSalary= "Belum Set Gaji";
}
   

    // Konversi outTime menjadi timestamp untuk perbandingan waktu
    // $outTimeTimestamp = $workData['outTime']->getTimestamp();
    // $overtime1Threshold = strtotime('18:00:00');

    // Hitung gaji lembur level 1 hanya jika outTime lebih dari 18:00
  

    // Mengembalikan detail komponen gaji
    return [
        'totalSalary' => $totalSalary,
        'totalNormalSalary' => $totalNormalSalary,
        'totalOvertime1Salary' => $totalOvertime1Salary,
        'totalOvertime2Salary' => $totalOvertime2Salary,
        'totalOvertime3Salary' => $totalOvertime3Salary,
        'grossSalary' => $grossSalary,
        'totalAllowance' => $totalAllowance,
        'totalDeduction' => $totalDeduction
    ];
}


// private function calculateSalary($workData, $salaryRate, $totalAllowance, $totalDeduction)
// {
//     // Hitung gaji normal berdasarkan jam kerja
//     $totalNormalSalary = $workData['totalWorkHours'] * $salaryRate['Gaji_Per_Jam'];

//     // Hitung gaji lembur level 1
//     $totalOvertime1Salary = $workData['totalOvertime1Hours'] * $salaryRate['Gaji_Per_Jam'];

//     // Hitung gaji lembur level 2
//     $totalOvertime2Salary = $workData['totalOvertime2Hours'] * $salaryRate['Gaji_Per_Jam'];

//     // Hitung gaji lembur level 3, biasanya untuk hari Minggu atau hari libur
//     $totalOvertime3Salary = $workData['totalOvertime3Hours'] * $salaryRate['Gaji_Per_Jam_Hari_Minggu'];

//     // Hitung total gaji sebelum tunjangan dan potongan
//     $grossSalary = $totalNormalSalary + $totalOvertime1Salary + $totalOvertime2Salary + $totalOvertime3Salary;

//     // Hitung gaji total setelah menambahkan tunjangan dan mengurangi potongan
//     $totalSalary = ($grossSalary + $totalAllowance) - $totalDeduction;

//     // Tentukan jam kerja yang diharapkan per hari (misal 8 jam)
//     // $expectedWorkHoursPerDay = 8;
//     // $daysWorked = $workData['daysWorked'] ?? 0;

//     // Hitung total jam kerja yang diharapkan berdasarkan jumlah hari kerja
//     // $expectedWorkHours = $daysWorked * $expectedWorkHoursPerDay;

//     // Jika total jam kerja normal kurang dari jam kerja yang diharapkan, kurangi gaji sebesar satu jam
//     // if ($workData['totalWorkHours'] < $expectedWorkHours) {
//     //     $totalSalary -= $salaryRate['Gaji_Per_Jam'];
//     // }

//     return $totalSalary;
// }
public function addEmployeeToPayroll()
{
    $detailModel = new MasterPenggajianDetailModel();

    // Data yang akan ditambahkan
    $data = [
        'penggajian_id' => $_POST["masterId"],
        'karyawan_id' => $_POST["employeeId"]
    ];

    // Cek apakah karyawan sudah ada dalam daftar penggajian dengan penggajian_id yang sama
    $existingEntry = $detailModel->where('penggajian_id', $data['penggajian_id'])
                                  ->where('karyawan_id', $data['karyawan_id'])
                                  ->first();

    if ($existingEntry) {
         header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      die(json_encode(array('message' => 'Karyawan sudah terdaftar dalam daftar penggajian ini.', 'code' => 4)));
    }

    // Menyimpan data ke database jika belum ada
    if ($detailModel->insert($data)) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Karyawan berhasil ditambahkan ke daftar penggajian.']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      die(json_encode(array('message' => 'Gagal menambahkan karyawan ke daftar penggajian.', 'code' => 4)));
    }
}
public function deleteEmployeeFromPayroll()
{
    // Mendapatkan instance dari request
    $request = \Config\Services::request();

    // Mengambil data dari request
    $employeeId = $request->getPost('employeeId');
    $masterId = $request->getPost('masterId');

    // Memastikan employeeId dan masterId diterima
    if (!$employeeId || !$masterId) {
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'ID karyawan atau ID master tidak ditemukan.'
        ]);
    }

    // Memulai koneksi ke database
    $mdl = new MasterPenggajianDetailModel();

    // Menjalankan query untuk menghapus data berdasarkan employeeId dan masterId
    try {
        $mdl->where('karyawan_id', $employeeId);
        $mdl->where('penggajian_id', $masterId);
        $mdl->delete();

        // Cek apakah data benar-benar dihapus
        if ($mdl->affectedRows() > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan atau sudah dihapus.'
            ]);
        }
    } catch (\Exception $e) {
        // Error handling jika terjadi kesalahan pada query
        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
        ]);
    }
}

 private function generateSalarySlipDetails($workData, $salaryRate, $totalAllowance, $totalDeduction)
{
    // Hitung gaji normal berdasarkan jam kerja
    $totalNormalSalary = $workData['totalWorkHours'] * $salaryRate['Gaji_Per_Jam'];



    // Hitung gaji lembur level 1 hanya jika outTime lebih dari 18:00
    $totalOvertime1Hours = $workData['totalOvertime1Hours'];
    $totalOvertime1Salary = $totalOvertime1Hours * $salaryRate['Gaji_Per_Jam'];

    // Membulatkan lembur level 2 dan 3 ke kelipatan 15 menit (0.25 jam)
    $totalOvertime2HoursRounded = ceil($workData['totalOvertime2Hours'] / 0.25) * 0.25;
    $totalOvertime3HoursRounded = ceil($workData['totalOvertime3Hours'] / 0.25) * 0.25;

    // Hitung gaji lembur level 2 dan 3 dengan waktu yang sudah dibulatkan
    $totalOvertime2Salary = $totalOvertime2HoursRounded * $salaryRate['Gaji_Per_Jam'];
    $totalOvertime3Salary = $totalOvertime3HoursRounded * $salaryRate['Gaji_Per_Jam'];

    // Hitung total gaji sebelum tunjangan dan potongan
    $grossSalary = $totalNormalSalary + $totalOvertime1Salary + $totalOvertime2Salary + $totalOvertime3Salary;

    // Hitung total gaji setelah tunjangan dan potongan

    $sunday = $workData['sundayWorkHours'] * $salaryRate['Gaji_Per_Jam_Hari_Minggu'];
    $netSalary = ($grossSalary +$sunday+ $totalAllowance) - $totalDeduction;
    
    // Struktur rincian untuk slip gaji
    return [
        'basic_salary' => number_format($totalNormalSalary, 2, ',', '.'), // Gaji Pokok
        'sunday_salary' => number_format($sunday, 2, ',', '.'), // Gaji Pokok
        'overtime1_salary' => number_format($totalOvertime1Salary, 2, ',', '.'), // Gaji Lembur Level 1
        'overtime2_salary' => number_format($totalOvertime2Salary, 2, ',', '.'), // Gaji Lembur Level 2
        'overtime3_salary' => number_format($totalOvertime3Salary, 2, ',', '.'), // Gaji Lembur Level 3
        'gross_salary' => number_format($grossSalary, 2, ',', '.'), // Gaji Kotor
        'allowances' => number_format($totalAllowance, 2, ',', '.'), // Tunjangan
        'deductions' => number_format($totalDeduction, 2, ',', '.'), // Potongan
        'net_salary' => number_format($netSalary, 2, ',', '.') // Gaji Bersih
    ];
}
public function getEmployeeSalarySlip($employeeId, $penggajianId)
{
    if (!$employeeId || !$penggajianId) {
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'ID karyawan atau ID penggajian tidak ditemukan.'
        ]);
    }

    // Inisialisasi model
    $penggajianDetailModel = new MasterPenggajianDetailModel();
    $allowanceModel = new MdlFemployeeAllowanceList();
    $deductionModel = new MdlFemployeeDeductionList();
    $effectiveHoursModel = new MdlEffectiveHours();
    $attendanceModel = new AttendanceModel();
    $salaryCatModel = new MdlSalaryCat();
    $salaryPatternModel = new \App\Models\MdlFsalaryPatternEmployee();
    // Ambil data penggajian dan karyawan
    $result = $penggajianDetailModel
        ->select('master_penggajian_detail.karyawan_id, pegawai.pegawai_nama, pegawai.pegawai_pin, master_penggajian.kode_penggajian, master_penggajian.tanggal_awal_penggajian, master_penggajian.tanggal_akhir_penggajian')
        ->join('master_penggajian', 'master_penggajian_detail.penggajian_id = master_penggajian.id', 'left')
        ->join('pegawai', 'master_penggajian_detail.karyawan_id = pegawai.pegawai_id', 'left')
        ->where('master_penggajian_detail.penggajian_id', $penggajianId)
        ->where('master_penggajian_detail.karyawan_id', $employeeId)
        ->first();

    if (!$result) {
        return $this->response->setStatusCode(404)->setJSON([
            'success' => false,
            'message' => 'Data slip gaji tidak ditemukan untuk karyawan ini.'
        ]);
    }

    // Hitung total allowance dan deduction
    $totalAllowance = $this->calculateTotalAllowance($allowanceModel, $employeeId);
    $totalDeduction = $this->calculateTotalDeduction($deductionModel, $employeeId);

    // Dapatkan data tarif gaji berdasarkan kategori atau pola gaji yang dimiliki karyawan
        $salaryRate = $salaryPatternModel
        ->select('employeesallarycat.Gaji_Pokok, employeesallarycat.Gaji_Per_Jam, employeesallarycat.Gaji_Per_Jam_Hari_Minggu')
        ->join('employeesallarycat', 'employeesallarycat.id = salary_pattern_employee.id_salary_pattern')
        ->where('salary_pattern_employee.id_employee', $employeeId)
        ->first();
    if (!$salaryRate) { 
        return $this->response->setStatusCode(404)->setJSON([
            'success' => false,
            'message' => 'Tarif gaji tidak ditemukan untuk karyawan ini.'
        ]);
    }

    // Mendapatkan data kehadiran dan memproses in_time dan out_time
    $attendanceRecords = $attendanceModel->getAttendance($result['pegawai_pin'], $employeeId, $result['tanggal_awal_penggajian'], $result['tanggal_akhir_penggajian']);
    $processedAttendance = $this->getInOutTimes($attendanceRecords);

    // Hitung total jam kerja dan lembur
    $workData = $this->calculateWorkAndOvertime($processedAttendance, $effectiveHoursModel);

    // Hitung total gaji
    $salaryDetails = $this->generateSalarySlipDetails($workData, $salaryRate, $totalAllowance, $totalDeduction);

    // Gabungkan hasil dengan detail slip gaji dan data kehadiran
    $result['salary_slip_details'] = $salaryDetails;
    $result['attendance_data'] = $processedAttendance;
    $result['salary_rate'] = $salaryRate;

    // Mengembalikan hasil dalam format JSON
    return $this->response->setJSON($result);
}

public function getSalaryRate($employeeId)
{
    // Cek apakah ID karyawan ada
    if (!$employeeId) {
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'ID karyawan tidak ditemukan.'
        ]);
    }

    // Inisialisasi model
    $salaryPatternModel = new \App\Models\MdlFsalaryPatternEmployee();
    $salaryCatModel = new \App\Models\MdlSalaryCat();

    // Lakukan join untuk mendapatkan rate salary berdasarkan id_employee
    $salaryPattern = $salaryPatternModel
        ->select('employeesallarycat.Gaji_Pokok, employeesallarycat.Gaji_Per_Jam, employeesallarycat.Gaji_Per_Jam_Hari_Minggu')
        ->join('employeesallarycat', 'employeesallarycat.id = salary_pattern_employee.id_salary_pattern')
        ->where('salary_pattern_employee.id_employee', $employeeId)
        ->first();

    if ($salaryPattern) {
        return $this->response->setJSON([
            'success' => true,
            'salary_rate' => [
                'Gaji_Pokok' => $salaryPattern['Gaji_Pokok'],
                'Gaji_Per_Jam' => $salaryPattern['Gaji_Per_Jam'],
                'Gaji_Per_Jam_Hari_Minggu' => $salaryPattern['Gaji_Per_Jam_Hari_Minggu']
            ]
        ]);
    } else {
        return $this->response->setStatusCode(404)->setJSON([
            'success' => false,
            'message' => 'Data salary rate tidak ditemukan untuk karyawan ini.'
        ]);
    }
}


}
