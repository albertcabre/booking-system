<?php
require_once('connection.php');
require_once('functions.php');

validate_user();

if ($request[operation] == "delete") {
    $r = mysqli_query("DELETE FROM user WHERE user_id=$request[delete_user_id]");
} elseif ($request[operation] == "add") {
    if (!filter_var($request[username], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $r = mysqli_query("INSERT INTO user (username,password,sub_id) VALUES ('$request[username]','".md5($request[password])."','1')");
        $request[username] = "";
        $request[password] = "";
    }
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<SCRIPT src="js/funciones.js"></SCRIPT>
<script language="javascript">
    function delete_username(user_id, username) {
        confirmation = confirm("Are you sure you want to delete username " + username + "?");
        if (confirmation) {
            document.myform.operation.value = "delete";
            document.myform.delete_user_id.value = user_id;
            document.myform.submit();
        }
    }

    function add_user() {
        if (document.myform.username.value !== "" && document.myform.password.value !== "") {
            if (document.myform.password.value.length < 5) {
                alert("Please password must have at least 5 digits")
            } else {
                document.myform.operation.value = "add";
                document.myform.submit();
            }
        } else {
            alert("Please specify a username and a password");
            document.myform.username.focus();
        }
    }
</script>
<table align="center" cellpadding="5" cellspacing="0">
    <tr>
        <?php
        if ($request[op] == "e") {
            ?>
            <td align="center">
                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'" onClick="document.location = 'admin.php?pagetoload=users_list.php'">
                    <a href="#" class="button_link">List Users</a>
                </div>
            </td>
            <?php
        } else {
            ?>
            <td align="center">
                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'" onClick="document.location = 'admin.php?pagetoload=users_list.php&op=e'">
                    <a href="#" class="button_link">Edit Users</a>
                </div>
            </td><?php
        }
        ?>
    </tr>
</table>

<table align="center" border="0" cellpadding="4" cellspacing="0">
    <form name="myform" method="post" action="admin.php">
        <input type="hidden" name="pagetoload" value="users_list.php">
        <input type="hidden" name="operation">
        <input type="hidden" name="delete_user_id">
        <input type="hidden" name="op" value="e">
        <tr class="header"><td class="titol_taula_list">Username</td><td class="titol_taula_list">Password</td></tr>
        <?php
        $r = mysqli_query("SELECT * FROM user WHERE sub_id=1 ORDER BY username");
        $class = "file1";
        while ($data = mysqli_fetch_assoc($r)) {
            ?>
            <tr class="row1">
                <td class="cell" style="text-align: left"><?= $data['username']; ?></td>
                <td class="cell" style="text-align: left"><?= "encrypted" ?></td>
                <?php
                if ($request[op] == "e") {
                    ?>
                    <td class="cell" align="center">
                        <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'" onClick="delete_username('<?= $data[user_id] ?>', '<?= $data[username] ?>')">
                            <a href="#" class="button_link">Delete</a>
                        </div>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }

        if ($request[op] == "e") {
            ?>
            <tr class="row1">
                <td class="cell"><input type="text" name="username" size="50" value="<?=$request[username]?>"></td>
                <td class="cell"><input type="password" name="password" size="20" value="<?=$request[password]?>"></td>
                <td class="cell" align="center">
                    <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'" onClick="add_user()">
                        <a href="#" class="button_link">Add</a>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
    </form>
</table>
<?php
if ($emailErr !== "") {
    echo "<p class=normal_text_red>$emailErr</p>";
}
?>
<br><br>