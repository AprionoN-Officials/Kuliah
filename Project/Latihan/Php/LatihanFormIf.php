<!DOCTYPE html>
<html>
<head>
    <title>Form KHS Mahasiswa</title>
</head>
<body>

    <div id="tampilanForm">
        <table>
            <tr>
                <td colspan="2"><b>FORM KHS</b></td>
            </tr>
            
            <tr>
                <td>
                    <label>NPM</label><br>
                    <input type="text" id="inputNPM">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nama</label><br>
                    <input type="text" id="inputNama">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Jurusan</label><br>
                    <select id="inputJurusan">
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Jenis Kelamin</label><br>
                    <select id="inputJenkel">
                        <option value="Pria">Pria</option>
                        <option value="Wanita">Wanita</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nilai UTS</label><br>
                    <input type="number" id="inputUTS" max="100">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nilai UAS</label><br>
                    <input type="number" id="inputUAS" max="100">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nilai TUGAS</label><br>
                    <input type="number" id="inputTugas" max="100">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Jumlah Hadir</label><br>
                    <input type="number" id="inputHadir" max="14">
                </td>
            </tr>
            <tr>
                <td>
                    <button type="button" onclick="prosesData()">Submit</button>
                </td>
            </tr>
        </table>
    </div>

    <div id="tampilanHasil" style="display: none;">
        
        <table border="1" width="100%" cellpadding="10">
            <tr>
                <td colspan="2"><b>Kartu Hasil Study</b></td>
            </tr>
            <tr>
                <td width="50%" valign="top">
                    <p>NPM : <span id="hasilNPM"></span></p>
                    <p>NAMA : <span id="hasilNama"></span></p>
                    <p>JURUSAN : <span id="hasilJurusan"></span></p>
                    <p>JENIS KELAMIN : <span id="hasilJenkel"></span></p>
                    <hr>
                    <p>NILAI UTS : <span id="hasilUTS"></span></p>
                    <p>NILAI UAS : <span id="hasilUAS"></span></p>
                    <p>NILAI TUGAS : <span id="hasilTugas"></span></p>
                    <p>HADIR : <span id="hasilHadir"></span></p>
                </td>

                <td width="50%" valign="top">
                    <p>Nilai Akhir : <span id="hasilAkhir"></span></p>
                    <br>
                    <h1 id="statusKelulusan"></h1> 
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button onclick="kembaliKeForm()">Back</button>
                </td>
            </tr>
        </table>

    </div>

    <script>
        function prosesData() {
            var npm = document.getElementById("inputNPM").value;
            var nama = document.getElementById("inputNama").value;
            var jurusan = document.getElementById("inputJurusan").value;
            var jenkel = document.getElementById("inputJenkel").value;
            
            var uts = parseInt(document.getElementById("inputUTS").value) || 0;
            var uas = parseInt(document.getElementById("inputUAS").value) || 0;
            var tugas = parseInt(document.getElementById("inputTugas").value) || 0;
            var hadir = document.getElementById("inputHadir").value;

            if (npm == "" || nama == "" || inputUTS.value == "") {
                alert("Data Kosong! Silahkan Isi Data Terlebih Dahulu.");
            return;
            }

            var nilaiAkhir = (uts * 0.3) + (uas * 0.4) + (tugas * 0.2) + ((hadir / 16) * 10);

            if (nilaiAkhir >= 85) {
                var status = "A";
            } else if (nilaiAkhir > 75 & nilaiAkhir <= 85) {
                var status = "B";
            } else if (nilaiAkhir > 65 & nilaiAkhir <= 75) {
                var status = "C";
            } else if (nilaiAkhir > 55 & nilaiAkhir <= 65) {
                var status = "D";
            } else {
                var status = "E";
            }
            
            document.getElementById("hasilNPM").innerText = npm;
            document.getElementById("hasilNama").innerText = nama;
            document.getElementById("hasilJurusan").innerText = jurusan;
            document.getElementById("hasilJenkel").innerText = jenkel;
            
            document.getElementById("hasilUTS").innerText = uts;
            document.getElementById("hasilUAS").innerText = uas;
            document.getElementById("hasilTugas").innerText = tugas;
            document.getElementById("hasilHadir").innerText = hadir;

            document.getElementById("hasilAkhir").innerText = nilaiAkhir.toFixed(2);
            document.getElementById("statusKelulusan").innerText = status;

            document.getElementById("tampilanForm").style.display = "none";
            document.getElementById("tampilanHasil").style.display = "block";
        }

        function kembaliKeForm() {
            document.getElementById("tampilanForm").style.display = "block";
            document.getElementById("tampilanHasil").style.display = "none";
        }
    </script>
</body>
</html>