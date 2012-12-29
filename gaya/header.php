<html>
<head>
    <title>CSI Gaya</title>
    <style type="text/css">
        h3 { padding: 0px; margin: 0px; color: white; font-style:italic;}
        h2 { padding: 0px; margin: 0px; color: white; font-style:italic;}
        body { padding: 10px; }
        td { color: white; }
    </style>
</head>

<body file=l background="file:///opt/sybhttpd/localhost.images/hd/bg.jpg" focushighlight="user5" bgcolor="#398EC4" focustext="user3" marginheight=0 marginwidth=0>

    <table>
        <tr>
            <td><img src="images/logo.png"></td>
            <td width="20">&nbsp;</td>
            <td>
                <h2>NMT Community Software Installer Gaya</h2>
                <form name="SelectCategory" method="post" action="index.php">
                    <input type="submit" name="toscreen" value="apps">
                    <?php if (count($aUpdates) > 0) { ?>
                    <input type="submit" name="toscreen" value="updates">
                    <?php } ?>
                    <input type="submit" name="toscreen" value="themes">
                    <input type="submit" name="toscreen" value="menus">
                    <input type="submit" name="toscreen" value="wait_images">
                    <input type="submit" name="toscreen" value="webservices">
                    <input type="submit" name="toscreen" value="installed">
                </form>
            </td>
        </tr>
    </table>

    <table width="720">
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width=100%>
                    <tr>
                        <td width="12" height="12"><img src="images/tbl_topleft.png" width="12" height="12"></td>
                        <td height="12" background="images/tbl_topcenter.png"></td>
                        <td width="12" height="12"><img src="images/tbl_topright.png" width="12" height="12"></td>
                    </tr>
                    <tr>
                        <td width="12" height="12" background="images/tbl_leftside.png"></td>
                        <td background="images/tbl_center.png">
                            <br>
