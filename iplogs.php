<!DOCTYPE html>
<html>
<head>
    <title>IP Log</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <center>
        <h1>IP Hareketleri</h1>
    </center>
    
    <!-- SİL butonunu ekleyin -->
    <button onclick="deleteLogFile()">TÜM LOGLARI TEMİZLE</button>

    <table>
        <tr>
            <th>S.NO</th>
            <th>IP</th>
            <th>USERAGENT</th>
            <th>ÜLKE</th>
            <th>SAAT</th>
            <th>TARAYICI DİLİ</th>
            <th>BILGI</th>
        </tr>
    </table>

    <style>
        .bot-giris {
            background-color: red;
            color: white;
            font-weight: bold;
        }
        .referer-yok {
            background-color: yellow;
            color: black;
            font-weight: bold;
        }
        .normal-giris {
            background-color: green;
            color: white;
            font-weight: bold;
        }
    </style>

    <script>
        var ipCounts = {};

        function updateIPLog() {
            // AJAX isteği ile yeni verileri al
            $.get('get_iplog.php', function(data) {
                // Alınan verileri tabloya ekle
                var table = $('table');
                table.find('tr:gt(0)').remove();
                table.append(data);

                // Durum sütununu güncelle
                updateStatus();
                markRowsWithConditions();
            });
        }

        function addNewRow(data) {
            var newRow = $('<tr>');
            newRow.append('<td>' + data.sno + '</td>');
            newRow.append('<td>' + data.ip + '</td>');
            newRow.append('<td>' + data.useragent + '</td>');
            newRow.append('<td>' + data.country + '</td>');
            newRow.append('<td>' + data.time + '</td>');
            newRow.append('<td>' + data.browserlang + '</td>');
            newRow.append('<td class="' + determineRowStyle(data.info) + '">' + data.info + '</td>');
            
            var table = $('table');
            table.find('tr:eq(1)').after(newRow); // En üst sıraya ekle
        }

        function determineRowStyle(info) {
            if (info.includes('GOOGLE BOT') || info.includes('BOT GIRISI') || info.includes('TR DISI GIRISI') || info.includes('XSS ATTACK GIRISI') || info.includes('IPV-6 GIRISI') || info.includes('MANIPULE DENEME') || info.includes('URL DENEME GIRISI') || info.includes('var ise')) {
                return 'bot-giris';
            } else if (info === 'REFERER Yok') {
                return 'referer-yok';
            } else if (info === 'NORMAL GIRIS') {
                return 'normal-giris';
            } else {
                return ''; // Diğer durumlar için stil uygulama
            }
        }

        // Dosyayı silen fonksiyon
        function deleteLogFile() {
            if (confirm("IP log dosyasını silmek istediğinizden emin misiniz?")) {
                $.get('delete_logfile.php', function(response) {
                    if (response === 'success') {
                        alert('IP log dosyası başarıyla silindi.');
                        updateIPLog(); // Tabloyu güncelle
                    } else {
                        alert('IP log dosyası silinemedi.');
                    }
                });
            }
        }

        function updateStatus() {
            var currentTime = new Date().getTime();
            $('table tr:gt(0)').each(function() {
                var row = $(this);
                var ipCell = row.find('td:nth-child(2)').text();
                var timeCell = row.find('td:nth-child(5)').text();
                var time = new Date(timeCell).getTime();
                var statusCell = row.find('td:nth-child(8)');
                var ipXCell = row.find('td:nth-child(9)');
                
                if (!ipCounts[ipCell]) {
                    ipCounts[ipCell] = 1;
                } else {
                    ipCounts[ipCell]++;
                }

                if (currentTime - time <= 3000) {
                    statusCell.text('Online');
                } else {
                    statusCell.text('Offline');
                }

                ipXCell.text(ipCounts[ipCell]);
            });
        }

        function markRowsWithConditions() {
            $('table tr:gt(0)').each(function() {
                var row = $(this);
                var bilgiCell = row.find('td:nth-child(7)');
                var styleClass = determineRowStyle(bilgiCell.text());
                bilgiCell.addClass(styleClass);
            });
        }

        // Belirli aralıklarla verileri ve durumu güncellemek için setInterval kullan
        setInterval(updateIPLog, 3000);

        // Sayfa yüklendiğinde verileri ve durumu ilk kez al
        updateIPLog();
    </script>
</body>
</html>
