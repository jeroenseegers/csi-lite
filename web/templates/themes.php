<h3>Themes</h3>
 <table>
      <tr>
        <td colspan=3  align="right">
            <b>Select a Theme</b><br>
        </td>
    </tr>
     <tr>
         <td valign="top">
             <table>
                 <tr>
                     <td valign="top" align="right">Name</td>
                     <td><input readonly size="40" name="Name" value="<?php echo $oSelectedItem->Name." ".$oSelectedItem->Version; ?>"><br>
                     <form name="frmMain" method="post" action="index.php">
                         <input type="submit" name="action" value="Install"><input type="submit" name="action" value="Uninstall" onclick="return confirm('This will uninstall ALL\ninstalled Themes\nAre you sure?');">
                     </form></td>
                 </tr>
                 <tr>
                     <td valign="top" align="right">Version</td>
                     <td><input readonly size="40" name="Version" value="<?php echo $oSelectedItem->Version; ?>"></td>
                 </tr>
                 <tr>
                     <td valign="top" align="right">Author</td>
                     <td><input readonly size="40" name="Author" value="<?php echo $oSelectedItem->Author; ?>"></td>
                 </tr>
                 <tr>
                     <td valign="top" align="right">Description</td>
                     <td><textarea readonly rows="5" cols="40"><?php echo $oSelectedItem->Description; ?></textarea></td>
                 </tr>
             </table>
         </td>
         <td width="20">
         </td>
         <td valign="top" align="right">
             <form name="frmSelectApplication" method="post" action="index.php">
                 <select name="SelectedItem" size="13" style="width: 200px;" onchange="document.forms['frmSelectApplication'].submit();">
                     <?php echo $sOptions; ?>
                 </select>
             </form>
         </td>
     </tr>
 </table>
