<h3>Installed applications</h3>
<table>
     <tr>
        <td colspan=3  align="right">
            <b>Select an Installed Application</b><br>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <table>
                <tr>
                    <td valign="top" align="right" width="100">Name</td>
                    <td><input readonly size="40" name="Name" value="<?php echo $selecteditem['Name']." ".$selecteditem['Version']; ?>"><br>
                    <form name="frmMain" method="post" action="index.php">
                        <input type="submit" name="action" <?php if ($selecteditem['Started']=="1") echo "disabled"; ?> value="start">
                        <input type="submit" name="action" <?php if ($selecteditem['Started']=="0") echo "disabled"; ?> value="stop">
                        <input type="submit" name="action" <?php if ($selecteditem['Started']=="0") echo "disabled"; ?> value="restart">
                        <input type="submit" name="action" value="uninstall" onclick="return (confirm('Are you sure you want\nto uninstall\n<?php echo $selecteditem['Name']." ".$selecteditem['Version']; ?>?'));">
                    </form></td>
                </tr>
                <tr>
                    <td valign="top" align="right">Start on boot</td>
                    <td><form name="frmEnable" method="post" action="index.php"><input type="submit" name="action" <?php if ($selecteditem['Enabled']=="1") echo "disabled"; ?> value="enable">
                    <input type="submit" name="action" <?php if ($selecteditem['Enabled']=="0") echo "disabled"; ?> value="disable">
                    </form>
                    </td>
                </tr>
            </table>
        </td>
        <td width="20">
        </td>
        <td valign="top" align="right">
            <form name="frmSelectApplication" method="post" action="index.php">
                <select name="SelectedItem" size="13" style="width: 200px;" onchange="document.forms['frmSelectApplication'].submit();">
                    <?php echo $options; ?>
                </select>
            </form>
        </td>
    </tr>
</table>
