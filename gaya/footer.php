                        </td>
                        <td width="12" height="12" background="images/tbl_rightside.png"></td>
                    </tr>
                    <tr>
                        <td width="12" height="12"><img src="images/tbl_bottomleft.png"></td>
                        <td background="images/tbl_bottomcenter.png"></td>
                        <td width="12" height="12"><img src="images/tbl_bottomright.png"></td>
                    </tr>
                    </table>

                    <?php if (isset($sInstallResult) || (!empty($sScreenshots))) : ?>
                    <br>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="12" height="12"><img src="images/tbl_topleft.png" width="12" height="12"></td>
                            <td height="12" background="images/tbl_topcenter.png"></td>
                            <td width="12" height="12"><img src="images/tbl_topright.png" width="12" height="12"></td>
                        </tr>
                        <tr>
                            <td width="12" height="12" background="images/tbl_leftside.png"></td>
                            <td background="images/tbl_center.png">
                            <?php if (isset($sInstallResult)) : ?>
                                <h3>Installation finished, install result:</h3>
                                <textarea readonly rows="5" cols="80"><?php echo $sInstallResult; ?></textarea>
                                <?php unset($sInstallResult); ?>
                            <?php elseif (!empty($sScreenshots)) : ?>
                                <?php echo $sScreenshots; ?>
                            <?php endif; ?>
                            </td>
                            <td width="12" background="images/tbl_rightside.png"></td>
                        </tr>
                        <tr>
                            <td width="12" height="12"><img src="images/tbl_bottomleft.png" width="12" height="12"></td>
                            <td height="12" background="images/tbl_bottomcenter.png"></td>
                            <td width="12" height="12"><img src="images/tbl_bottomright.png" width="12" height="12"></td>
                        </tr>
                    </table>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <font size="-2"><i>NMT Community Software Installer for Gaya. CSI Gaya version <?php echo $aSettings['VERSION']; ?>. Created by Ger Teunis &amp; Jeroen Seegers.</i></font>
        <?php if ((count($aUpdates) > 0) && ($sScreen != 'Updates')) { ?> <script>alert('Application updates\nare available.\nCheck Updates screen.');</script> <?php } ?>
    </body>
</html>
