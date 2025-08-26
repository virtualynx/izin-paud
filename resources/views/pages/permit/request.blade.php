@extends('app')

@section('title', config('app.name').'- Pengajuan')

@section('breadcrumbs', 'Pengajuan Izin')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Informasi Tentang PAUD</h4>
                </div>

                <div class="card-body">
                    {{-- Input tentang PAUD --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Upload Dokumen Persyaratan Izin Operasional PAUD</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Silakan upload dokumen persyaratan sesuai dengan daftar di bawah ini. Format file yang diterima: PDF, JPG, JPEG, PNG (Maks. 2MB per file)</p>
                    
                    <form id="paudDocumentForm">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="60%">Persyaratan</th>
                                        <th width="35%">Upload Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                {{-- <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>KTP Pembina (Ketua Yayasan/ Ketua Lembaga /Direktur /Penanggung Jawab)</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_ktp" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Akta Yayasan dan Pengesahannya serta Akta Perubahannya (bila ada perubahan)</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_akta" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Surat kuasa bermeterai Rp. 10.000 dan stempel apabila pengurusan dikuasakan kepada orang lain, dengan melampirkan foto kopi KTP yang diberi kuasa</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_kuasa" accept=".pdf,.jpg,.jpeg,.png">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Persetujuan / Dukungan Warga Setempat di ketahui RT/RW</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_dukungan_warga" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Persetujuan / Dukungan dari Satuan Pendidikan yang terdekat yang sejenis (min 2 satuan pendidikan)</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_dukungan_sekolah" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Surat Keterangan Domisili sarana lembaga (Satuan Pendidikan) dari Kepala Desa/Lurah</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_domisili" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Rekomendasi dari Penilik (Uji Kelayakan 8 Standar Nasional Pendidikan)</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_rekomendasi" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>Surat pernyataan kebenaran keabsahan data bermaterai Rp. 10.000, khusus untuk yang berbadan usaha atau hukum wajib menggunakan Kop Surat dan stempel lembaga</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_pernyataan" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>Keputusan Ketua Yayasan tentang pendirian lembaga satuan pendidikan PAUD</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_keputusan_pendirian" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td>Keputusan Ketua Yayasan tentang Pengangkatan Kepala Sekolah dan Guru</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_keputusan_guru" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>11</td>
                                        <td>Surat Tanah dan Bangunan atas nama Pribadi/Yayasan/Perusahaan (SHM/AJB /Akta Hibah /ikrar Wakaf). Apabila bukan milik sendiri/Yayasan/Perusahaan, lampirkan Akta Sewa-Menyewa/Pinjam Pakai (Untuk masa sewa/Pinjam Pakai paling sedikit 5 (lima) tahun)</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_tanah" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>Luas lahan min 300m2 dengan ruang belajar 2 (dua) ruang belajar</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_lahan" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>13</td>
                                        <td>Rencana Induk Pengembangan Sekolah (RIPS)</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_rips" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>14</td>
                                        <td>Rencana Pencapaian Standar Penyelenggaraan Pendidikan, mengacu pada Standar Nasional Pendidikan</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_rencana" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>15</td>
                                        <td>Susunan nama lengkap, jabatan pengurus dan rincian tugas</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_susunan_pengurus" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>16</td>
                                        <td>Dokumen kemampuan pembiayaan operasional kegiatan belajar mengajar, minimal satu tahun ajaran, dengan dibuktikan Rekening Yayasan</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_keuangan" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>17</td>
                                        <td>Daftar sarana dan prasarana pendidikan yang sesuai ketentuan standar</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_sarana" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>18</td>
                                        <td>Foto sarana dan prasarana pendidikan yang diajukan, mulai dari tampak depan hingga ruang kelas, hingga lingkungan sekitarnya, banyaknya foto 5-10 lembar</td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="doc_foto" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                                            <small class="text-muted">Pilih multiple file untuk upload beberapa foto</small>
                                        </td>
                                    </tr>
                                </tbody> --}}
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    Saya menyatakan bahwa semua dokumen yang diupload adalah benar dan valid
                                </label>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Submit Dokumen</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--  --}}
@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: `{{ url('/api/permit/docrec/list') }}`,
                type: 'GET',
                success: function(res) {
                    console.log('res', res);
                    if(res.status == 0){
                        Modal.show(`
                            <span class="text-success">res: ${res} !!</span>
                        `);
                    }else{
                        Modal.show(
                            `
                                <span class="text-danger">
                                    <div>${res.message}</div>
                                </span>
                            `,
                            `<span class="text-danger">Error</span>`
                        );
                    }
                },
                error: Utils.ajaxErrorHandler
            });
        });
    </script>
@endpush

{{-- form submit event --}}
@push('scripts')
    <script>
        document.getElementById('paudDocumentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate file sizes
            let valid = true;
            const inputs = this.querySelectorAll('input[type="file"]');
            
            inputs.forEach(input => {
                if (input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        if (input.files[i].size > 2 * 1024 * 1024) { // 2MB limit
                            alert(`File ${input.files[i].name} melebihi ukuran maksimum 2MB`);
                            valid = false;
                        }
                    }
                }
            });
            
            if (valid) {
                // Show success modal
                Modal.show(
                    '<p>Dokumen persyaratan PAUD berhasil diupload. Silakan tunggu proses verifikasi dari pihak terkait.</p>',
                    'Upload Berhasil',
                    {
                        label: 'OK', 
                        callback: function() {
                            Modal.show();
                            // In a real application, you would submit the form here
                            // document.getElementById('paudDocumentForm').submit();
                        }
                    }
                );
            }
        });
    </script>
@endpush