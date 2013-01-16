<h3>Settings</h3>
<form name="frmMain" method="post" action="index.php">
    <table>
         <tr>
            <td colspan="2">
                <p><font size="2">Only change if you know what you're doing!</font></p>
            </td>
        </tr>
        <tr>
            <td colspan="2">Device type:</td>
        </tr>
        <tr>
            <td>PCH A-400</td>
            <td>
                <?php if ($aSettings['DEVICE_TYPE'] != 'A-400') { ?>
                    <button type="submit" name="device" value="A-400">Enable</button>
                <?php } else { ?>
                    <p>Current</p>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>PCH A/C-300</td>
            <td>
                <?php if ($aSettings['DEVICE_TYPE'] != 'A/C-300') { ?>
                    <button type="submit" name="device" value="A/C-300">Enable</button>
                <?php } else { ?>
                    <p>Current</p>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>PCH A/C-200</td>
            <td>
                <?php if ($aSettings['DEVICE_TYPE'] != 'A/C-200') { ?>
                    <button type="submit" name="device" value="A/C-200">Enable</button>
                <?php } else { ?>
                    <p>Current</p>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>PCH A-1xx/B-110</td>
            <td>
                <?php if ($aSettings['DEVICE_TYPE'] != 'A-1xx/B-110') { ?>
                    <button type="submit" name="device" value="A-1xx/B-110">Enable</button>
                <?php } else { ?>
                    <p>Current</p>
                <?php } ?>
            </td>
        </tr>
    </table>
</form>
