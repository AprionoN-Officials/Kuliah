<!DOCTYPE html>
<html>
<head>
    <title>Login Keren</title>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
            
            display: flex;
            justify-content: center;
            align-items: center;
            
            height: 100vh;
            margin: 0;
        }

        .kotak-login {
            background-color: white;
            width: 300px; 
            padding: 30px;
            
            border-radius: 10px; 
            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            
            text-align: center; 
        }

        input[type="text"], input[type="password"] {
            width: 100%; 
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box; 
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838; 
        }
    </style>
</head>
<body>

    <div class="kotak-login" id="boxLogin">
        <h2>Form Login</h2>
        
        <div style="text-align: left;"> 
            <label>Username:</label>
            <input type="text" placeholder="" id="username">
            
            <label>Password:</label>
            <input type="password" placeholder="" id="password">
        </div>

        <br>
        <button onclick="login()">Login</button>
    </div>

    <div id="tampilanLogin" style="display: none;">
        <h1>Ini Halaman Login</h1>
        <h2>Selamat Datang, <span id="namaUser"></span>!</h2>
        <p>Anda telah berhasil login.</p>
        <button onclick="logout()">Logout</button>
    </div>

    <script>
        function login() {
            var user = document.getElementById("username").value;
            var pass = document.getElementById("password").value;

            if (user == "admin" && pass == "123") {
                
                alert("Login Berhasil!");
             
                document.getElementById("boxLogin").style.display = "none";
    
                document.getElementById("tampilanLogin").style.display = "block";

                document.getElementById("namaUser").innerText = user;

            } else {
                alert("Login Gagal! Username atau Password salah.");
            }
        }

        function logout() {
            document.getElementById("boxLogin").style.display = "block";
            document.getElementById("tampilanLogin").style.display = "none";
            
            document.getElementById("username").value = "";
            document.getElementById("password").value = "";
        }
    </script>

</body>
</html>