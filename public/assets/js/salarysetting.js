var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";
       $(document).ready(function() {
            loadDataSC();
            loadData();
            loadAllowanceData();
            loadDeductionData();
        });
function loadData() {
            $.ajax({
                url: base_url + '/user/getDataWorkDay',
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    let no = 0
                    const tbody = $('#workScheduleTable tbody');
                    tbody.empty(); // Kosongkan tabel sebelum mengisi data baru
                    $.each(data, function(index, item) {
                        tbody.append(`
                            <tr>
                                <td>${no+=1}</td>
                                <td>${item.day}</td>
                                <td>${item.work_start}</td>
                                <td>${item.work_break}</td>
                                <td>${item.work_end}</td>
                                <td>${item.overtime_break}</td>
                                <td>${item.overtime_start}</td>
                                <td>${item.overtime_end}</td>
                                <td><a href="javascript:void(0);" class="btn btn-danger btn-sm hapus" day="${item.day}"   id="${item.id}" ">Hapus</a></td>
                            </tr>
                        `);
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }
        $('#workScheduleTable tbody').on('click','.hapus',function(){
                const id = $(this).attr('id');
                const day = $(this).attr('day');
                Swal.fire({
                    title: 'Anda yakin ingin menghapus hari '+day+'?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: base_url + '/user/deleteWorkDay', 
                            type: 'post',
                            data: { id: id },
                            success: function(response) {
                                Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                                loadData(); // Panggil loadData untuk memperbarui tampilan
                            },
                            error: function(xhr) {
                                let d = JSON.parse(xhr.responseText);
                                Swal.fire('Oops...', d.message, 'error');
                            }
                        });
                    }
                });
            });

$('.addDay').on('click', function() {
    const dayOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    Swal.fire({
        title: `Tambah Jam Kerja`,
        html: `<form id="form_add_data">
                <div class="form-group">
                    <label for="day">Hari</label>
                    <select class="form-control" id="day">
                        ${dayOptions.map(day => `<option value="${day}">${day}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group">
                    <label for="work_start">Jam Mulai Kerja</label>
                    <input type="time" class="form-control" id="work_start" required>
                </div>
                <div class="form-group">
                    <label for="work_end">Jam Selesai Kerja</label>
                    <input type="time" class="form-control" id="work_end" required>
                </div>
                <div class="form-group">
                    <label for="overtime_start">Jam Mulai Lembur</label>
                    <input type="time" class="form-control" id="overtime_start">
                </div>
                <div class="form-group">
                    <label for="overtime_end">Jam Selesai Lembur</label>
                    <input type="time" class="form-control" id="overtime_end">
                </div>
                <div class="form-group">
                    <label for="work_break">Jam Istirahat Kerja</label>
                    <input type="time" class="form-control" id="work_break">
                </div>
                <div class="form-group">
                    <label for="work_break_end">Jam Selesai Istirahat Kerja</label>
                    <input type="time" class="form-control" id="work_break_end">
                </div>
                <div class="form-group">
                    <label for="overtime_break">Jam Istirahat Lembur</label>
                    <input type="time" class="form-control" id="overtime_break">
                </div>
                <div class="form-group">
                    <label for="overtime_break_end">Jam Selesai Istirahat Lembur</label>
                    <input type="time" class="form-control" id="overtime_break_end">
                </div>
            </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
            const day = Swal.getPopup().querySelector('#day').value;
            const work_start = Swal.getPopup().querySelector('#work_start').value;
            const work_end = Swal.getPopup().querySelector('#work_end').value;
            const overtime_start = Swal.getPopup().querySelector('#overtime_start').value;
            const overtime_end = Swal.getPopup().querySelector('#overtime_end').value;
            const work_break = Swal.getPopup().querySelector('#work_break').value;
            const work_break_end = Swal.getPopup().querySelector('#work_break_end').value;
            const overtime_break = Swal.getPopup().querySelector('#overtime_break').value;
            const overtime_break_end = Swal.getPopup().querySelector('#overtime_break_end').value;

            if (!day || !work_start || !work_end) {
                Swal.showValidationMessage('Silakan lengkapi data hari, jam mulai, dan jam selesai kerja');
            }
            return { work_break_end,overtime_break_end,day, work_start, work_end, overtime_start, overtime_end, work_break, overtime_break };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: base_url + '/user/addEffectiveHours', // Ganti dengan URL yang sesuai
                data: {
                    day: result.value.day,
                    work_start: result.value.work_start,
                    work_end: result.value.work_end,
                    overtime_start: result.value.overtime_start,
                    overtime_end: result.value.overtime_end,
                    work_break: result.value.work_break,
                    work_break_end: result.value.work_break_end,
                    overtime_break: result.value.overtime_break,
                    overtime_break_end: result.value.overtime_break_end
                },
                success: function(data) {
                    
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: `Jam kerja berhasil ditambahkan.`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    let d = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `${d.message}`,
                        footer: '<a href="">Why do I have this issue?</a>'
                    });
                }
            });
        }
    });
});
//SALARY CAT


        // Panggil loadData saat halaman dimuat
        function loadDataSC() {
    $.ajax({
        url:  base_url + '/user/getSalaryCat',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            let no = 0;
            const tbody = $('#salaryCatTable tbody');
            tbody.empty(); // Empty the table before adding new data
            $.each(data, function(index, item) {
                tbody.append(`
                    <tr>
                        <td>${++no}</td>
                        <td>${item.Kode}</td>
                        <td>${item.Nama}</td>
                        <td>${item.Kategori}</td>
                        <td>${formatRupiah(item.Gaji_Pokok)}</td>
                        <td>${formatRupiah(item.Gaji_Per_Jam)}</td>
                        <td>${formatRupiah(item.Gaji_Per_Jam_Hari_Minggu)}</td>
                        <td><a href="javascript:void(0);" class="btn btn-danger btn-sm hapus" nama="${item.Nama}" id="${item.id}">Hapus</a></td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error(xhr);
        }
    });
}

$('#salaryCatTable tbody').on('click', '.hapus', function() {
    const id = $(this).attr('id');
    const nama = $(this).attr('nama');
    Swal.fire({
        title: 'Anda yakin ingin menghapus kategori gaji ' + nama + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:  base_url +'/user/deleteSalaryCat',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                    loadDataSC(); // Reload data after deletion
                },
                error: function(xhr) {
                    let d = JSON.parse(xhr.responseText);
                    Swal.fire('Oops...', d.message, 'error');
                }
            });
        }
    });
});

$('.addSalaryCat').on('click', function() {
    const catOptions = ['Operator', 'Staf'];

    Swal.fire({
        title: 'Tambah Data Karyawan',
        html: `
            <form id="form_add_data">
                <div class="form-group">
                    <label for="Kode">Kode</label>
                    <input type="text" class="form-control" id="Kode" required>
                </div>
                <div class="form-group">
                    <label for="Nama">Nama</label>
                    <input type="text" class="form-control" id="Nama" required>
                </div>
                <div class="form-group">
                    <label for="Kategori">Kategori</label>
                    <select class="form-control" id="Kategori">
                        ${catOptions.map(cat => `<option value="${cat}">${cat}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group">
                    <label for="Gaji_Pokok">Gaji Pokok</label>
                    <input type="number" class="form-control" id="Gaji_Pokok" required>
                </div>
                <div class="form-group">
                    <label for="Gaji_Per_Jam">Gaji Per Jam</label>
                    <input type="number" class="form-control" id="Gaji_Per_Jam" required>
                </div>
                <div class="form-group">
                    <label for="Gaji_Per_Jam_Hari_Minggu">Gaji Per Jam Hari Minggu</label>
                    <input type="number" class="form-control" id="Gaji_Per_Jam_Hari_Minggu" required>
                </div>
            </form>
        `,
        confirmButtonText: 'Tambah',
        focusConfirm: false,
        preConfirm: () => {
            const Kode = Swal.getPopup().querySelector('#Kode').value;
            const Nama = Swal.getPopup().querySelector('#Nama').value;
            const Kategori = Swal.getPopup().querySelector('#Kategori').value;
            const Gaji_Pokok = Swal.getPopup().querySelector('#Gaji_Pokok').value;
            const Gaji_Per_Jam = Swal.getPopup().querySelector('#Gaji_Per_Jam').value;
            const Gaji_Per_Jam_Hari_Minggu = Swal.getPopup().querySelector('#Gaji_Per_Jam_Hari_Minggu').value;

            if (!Kode || !Nama || !Kategori || !Gaji_Pokok || !Gaji_Per_Jam || !Gaji_Per_Jam_Hari_Minggu) {
                Swal.showValidationMessage('Semua data wajib diisi!');
            }

            return { Kode, Nama, Kategori, Gaji_Pokok, Gaji_Per_Jam, Gaji_Per_Jam_Hari_Minggu };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/user/addSalaryCat',
                data: result.value,
                success: function(data) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Data berhasil ditambahkan.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadDataSC(); // Reload data after adding
                },
                error: function(xhr) {
                    let d = JSON.parse(xhr.responseText);
                    Swal.fire('Oops...', d.message, 'error');
                }
            });
        }
    });
});

// Load Allowance Data
function loadAllowanceData() {
    $.ajax({
        url:  base_url + '/user/getAllowanceData',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let no = 0;
            const tbody = $('#allowanceTable tbody');
            tbody.empty(); // Empty the table before adding new data
            $.each(data, function(index, item) {
                tbody.append(`
                    <tr>
                        <td>${++no}</td>
                        <td>${item.Kode}</td>
                        <td>${item.Nama}</td>
                        <td>${item.Status}</td>
                        <td><a href="javascript:void(0);" class="btn btn-danger btn-sm deleteAllowance" id="${item.id}">Delete</a></td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error(xhr);
        }
    });
}

// Load Deduction Data
function loadDeductionData() {
    $.ajax({
        url:  base_url + '/user/getDeductionData',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let no = 0;
            const tbody = $('#deductionTable tbody');
            tbody.empty(); // Empty the table before adding new data
            $.each(data, function(index, item) {
                tbody.append(`
                    <tr>
                        <td>${++no}</td>
                        <td>${item.Kode}</td>
                        <td>${item.Nama}</td>
                        <td>${item.Status}</td>
                        <td><a href="javascript:void(0);" class="btn btn-danger btn-sm deleteDeduction" id="${item.id}">Delete</a></td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error(xhr);
        }
    });
}

// Delete Allowance
$('#allowanceTable tbody').on('click', '.deleteAllowance', function() {
    const id = $(this).attr('id');
    Swal.fire({
        title: 'Are you sure you want to delete this allowance?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:  base_url + '/user/deleteAllowance',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    Swal.fire('Deleted!', 'Allowance has been deleted.', 'success');
                    loadAllowanceData(); // Reload data after deletion
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to delete the allowance.', 'error');
                }
            });
        }
    });
});

// Delete Deduction
$('#deductionTable tbody').on('click', '.deleteDeduction', function() {
    const id = $(this).attr('id');
    Swal.fire({
        title: 'Are you sure you want to delete this deduction?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:  base_url + '/user/deleteDeduction',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    Swal.fire('Deleted!', 'Deduction has been deleted.', 'success');
                    loadDeductionData(); // Reload data after deletion
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to delete the deduction.', 'error');
                }
            });
        }
    });
});

// Add Allowance
$('.addAllowance').on('click', function() {
    AllowanceStatus = ["Tetap","Temporary"];
    Swal.fire({
        title: 'Add Allowance',
        html: `
            <form id="form_add_allowance">
                <div class="form-group">
                    <label for="Kode">Kode</label>
                    <input type="text" class="form-control" id="Kode" required>
                </div>
                <div class="form-group">
                    <label for="Nama">Nama</label>
                    <input type="text" class="form-control" id="Nama" required>
                </div>
                <div class="form-group">
                    <label for="Status">Status</label>
                    <select class="form-control" id="Status">
                        ${AllowanceStatus.map(st => `<option value="${st}">${st}</option>`).join('')}
                    </select>
                </div>
            </form>
        `,
        confirmButtonText: 'Add',
        focusConfirm: false,
        preConfirm: () => {
            const Kode = Swal.getPopup().querySelector('#Kode').value;
            const Nama = Swal.getPopup().querySelector('#Nama').value;
            const Status = Swal.getPopup().querySelector('#Status').value;

            if (!Kode || !Nama || !Status) {
                Swal.showValidationMessage('Please complete all fields!');
            }
            return { Kode, Nama, Status };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url:  base_url + '/user/addAllowance',
                data: result.value,
                success: function(data) {
                    Swal.fire('Success!', 'Allowance has been added.', 'success');
                    loadAllowanceData(); // Reload data after adding
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to add allowance.', 'error');
                }
            });
        }
    });
});

// Add Deduction
$('.addDeduction').on('click', function() {
    deductionStatus = ["Tetap","Temporary"];

    Swal.fire({
        title: 'Add Deduction',
        html: `
            <form id="form_add_deduction">
                <div class="form-group">
                    <label for="Kode">Kode</label>
                    <input type="text" class="form-control" id="Kode" required>
                </div>
                <div class="form-group">
                    <label for="Nama">Nama</label>
                    <input type="text" class="form-control" id="Nama" required>
                </div>
                <div class="form-group">
                    <label for="Status">Status</label>
                    <select class="form-control" id="Status">
                        ${deductionStatus.map(st => `<option value="${st}">${st}</option>`).join('')}
                    </select>
                </div>
            </form>
        `,
        confirmButtonText: 'Add',
        focusConfirm: false,
        preConfirm: () => {
            const Kode = Swal.getPopup().querySelector('#Kode').value;
            const Nama = Swal.getPopup().querySelector('#Nama').value;
            const Status = Swal.getPopup().querySelector('#Status').value;

            if (!Kode || !Nama || !Status) {
                Swal.showValidationMessage('Please complete all fields!');
            }
            return { Kode, Nama, Status };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url:  base_url + '/user/addDeduction',
                data: result.value,
                success: function(data) {
                    Swal.fire('Success!', 'Deduction has been added.', 'success');
                    loadDeductionData(); // Reload data after adding
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to add deduction.', 'error');
                }
            });
        }
    });
});
function formatRupiah(amount) {
    // Pastikan bahwa jumlah adalah angka atau dapat dikonversi menjadi angka
    if (isNaN(amount)) {
        return '0,00'; // Jika bukan angka, kembalikan default '0,00'
    }
    
    // Konversi nilai menjadi fixed-point dengan 2 angka desimal
    amount = parseFloat(amount).toFixed(2);

    // Pisahkan bagian desimal dan bagian integer
    let parts = amount.split('.');
    let integerPart = parts[0];
    let decimalPart = parts[1];

    // Tambahkan tanda pemisah ribuan
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Gabungkan kembali bagian integer dan desimal
    return 'Rp ' + integerPart + ',' + decimalPart;
}