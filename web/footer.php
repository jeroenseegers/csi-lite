            </td>
            <td width="12" height="12"></td>
        </tr>
        <tr>
            <td width="12" height="12"></td>
            <td></td>
            <td width="12" height="12"></td>
        </tr>
        </table>
        
        <?php
             if (isset($installresult) || (!empty($screenshots)))
             {
                ?>
                    <br>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="12" height="12"></td>
                        <td height="12"></td>
                        <td width="12" height="12"></td>
                    </tr>
                    <tr>
                        <td width="12" height="12"></td>
                        <td><?php
                            if (isset($installresult))
                            {
                                echo "<h3>Installation finished, install result:</h3>";
                                echo "<textarea readonly rows=\"5\" cols=\"80\">".$installresult."</textarea>";
                                unset($installresult);
                            }
                            else if (!empty($screenshots))
                            {
                                echo $screenshots;
                            }
                        ?>&nbsp;</td>
                        <td width="12"></td>
                    </tr>
                    <tr>
                        <td width="12" height="12"></td>
                        <td height="12"></td>
                        <td width="12" height="12"></td>
                    </tr>
                    </table>
                    <?php
                }
        ?>
        </td></tr></table>
        <font size="-2"><i>NMT Community Software Installer for Gaya. CSI Gaya version <?php echo $GLOBALS['VERSION']; ?>. Created by Ger Teunis.</i></font>
        <?php if ((count($updates)>0) && ($screen!="Updates")) { ?> <script>alert('Application updates\nare available.\nCheck Updates screen.');</script> <?php } ?>
    </body>
</html>
