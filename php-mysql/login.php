
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
}

.box {
    max-width: 500px;
    margin: 60px auto;
    padding: 35px;
    background: #ffffff;
    border-radius: 8px;
    border-top: 6px solid #0a9f67;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.box h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #0a9f67;
    letter-spacing: 2px;
}

.box table {
    margin: auto;
}

.box td {
    padding: 8px;
}

.box input[type="text"],
.box input[type="password"] {
    width: 240px;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    transition: 0.3s;
}

.box input:focus {
    border-color: #0a9f67;
    outline: none;
    box-shadow: 0 0 5px rgba(10,159,103,0.4);
}

.box button {
    padding: 8px 18px;
    border-radius: 5px;
    border: none;
    background: #0a9f67;
    color: #fff;
    cursor: pointer;
    margin: 5px;
    transition: 0.3s;
}

.box button:hover {
    background: #087a50;
}

/* Container tombol */
.form-button {
    text-align: center;
    margin-top: 15px;
}

/* Submit */
input[type="submit"] {
    background: #0a9f67;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
}

input[type="submit"]:hover {
    background: #087a50;
    box-shadow: 0 4px 10px rgba(10,159,103,0.4);
}

/* Reset */
input[type="reset"] {
    background: #6c757d;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
    margin-left: 10px;
}

input[type="reset"]:hover {
    background: #5a6268;
}

/* Responsive */
@media (max-width: 576px) {
    input[type="submit"],
    input[type="reset"] {
        width: 100%;
        margin: 5px 0;
    }
}


</style>
<div class="box">
<table>
<tr>
<td>
  <form action="login_act2.php" method="post">
      <table>
      <tr>
        <td colspan="3" align="center"><h2>LOGIN</h2></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td>Username</td>
        <td>:</td>
        <td><input type="text" name="p_username"></td>
      </tr>
      <tr>
        <td>Password</td>
        <td>:</td>
        <td><input type="password" name="p_pwd"></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3"><input type="submit" value="Login" class="btn-primary"> 
        <input type="reset" value="Cancel" class="btn-secondary"> 
     </td>
      </tr>

    </table>
  </form>
  <button onclick="window.location.href='form_login.php'" class="btn-secondary">Belum punya akun? Daftar disini..</button>
</td>
</tr>
</table>
</div>