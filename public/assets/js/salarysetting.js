var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

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

        // Panggil loadData saat halaman dimuat
        $(document).ready(function() {
            loadData();
        });