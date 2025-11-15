<div class="mainPage">
    <div class="content">

        <div class="left left_cont">

            <h1 class="printMainTitle media_kit_word">Media Kits
                <div class="text-gradient"></div>
            </h1>

            <div class="pring_home_gallery">
                <img src="/images/print_gall.jpg">
                <div class="rotatormainbutton">
                    <div name="sector1" class="rotatorbutton checked"></div>
                    <div name="sector2" class="rotatorbutton"></div>
                    <div name="sector3" class="rotatorbutton"></div>
                    <div name="sector4" class="rotatorbutton"></div>
                </div>
            </div>

            <div class="left print_info">
                Dignissim porttitor adipiscing, in ac, proin mattis? Phasellus rhoncus nunc egestas! Vel, odio proin mid pid rhoncus? Penatibus nunc magna tincidunt ac sagittis!
            </div>
            <div class="left print_info">
                <ul>
                    <li><span id="designImg"></span> Turpis aliquam, scelerisque habitasse</li>
                    <li><span id="designImg"></span> Dolor mattis, pid mauris hac</li>
                    <li><span id="designImg"></span> Nunc cras amet platea</li>
                    <li><span id="designImg"></span> Nec urna pellentesque sagittis</li>
                </ul>
            </div>

        </div>

        <menu class="mainRightMenu">
            <form action="/print_it/step2" id="print_form" name="print_form" method="post">
                <div class="custom">
                    <span class="helvetica f22 color_pink marg_b5">START HERE</span><br>
                    <span class="font_creighton f38">Build Your Media Kit</span>
                </div>
                <p class="customText">Select the items that you would like to include in your media kit. If you need help or have a special request, please give us a call at 1-800-900-5632.</p>
                <div class="customMenu_print">
                    <input type="checkbox" name="order[]" value="1" <?php if (@$_GET['get'] == 1 || (!empty($_POST['order']) && in_array(1, $_POST['order']))) echo'checked="checked"' ?> class="left unselect_child">
                    <p class="custommenuText"><span>Folder</span> <span class="normal_font">(required for media kit)</span></p>
                </div>
                <div class="customMenu_print sub">
                    <input type="checkbox" name="sub_order[1]" <?php
                    if (!empty($_POST['sub_order']) && in_array('1', $_POST['sub_order'])) {
                        echo'checked="checked"';
                    } elseif (empty($_POST['order']) || !in_array(1, $_POST['order'])) {
                        echo'disabled="disabled"';
                    }
                    ?> value="1" class="left">
                    <p class="custommenuText">Add Stitched-in Booklet/Catalog</p>
                    <div class="coating">

                        <div class="print_inn">
                            <div class="blockrelative">
                                <span class="coatingArrow">
                                    <div class="sel_show"></div>
                                </span>
                            </div>
                            <p class="coatTitle">Folder with Stitched-in Booklet/Catalog</p><br class="clear">
                            <img src="/images/mediaMoreBook.png" class="right" style="margin-top: -30px; margin-right: 10px;">
                            <p class="coatTopText">This is FPO copy for a brief description of the product. 4 line maximum. This is FPO copy for a brief description of the selected product. 4 line maximum. <br>
                                <br>    
                                Samples</p>
                            <br class="clear">
                            <div class="photo_one">
                                <img src="/images/mediaMoreCatImg.png"><br>
                                <small>Photo description</small>
                            </div>
                            <div class="photo_one">
                                <img src="/images/mediaMoreCatImg.png"><br>
                                <small>Photo description</small>
                            </div>
                            <div class="photo_one" style="margin: 0">
                                <img src="/images/mediaMoreCatImg.png"><br>
                                <small>Photo description</small>
                            </div>
                            <br class="clear">
                        </div>

                    </div>
                </div>
                <?php
                if (!empty($categories)) {
                    foreach ($categories as $val) {
                        if ($val['title'] != 'Folder') {
                            ?>
                            <div class="customMenu_print">
                                <input type="checkbox" name="order[]" value="<?= $val['id'] ?>" <?php if (@$_GET['get'] == $val['id'] || (!empty($_POST['order']) && in_array($val['id'], $_POST['order']))) echo'checked="checked"'; ?> class="left"><?php /* if(!empty($_POST['order']) && in_array($val['id'], $_POST['order']))echo'checked="checked"' */ ?>
                                <p class="custommenuText"><?= $val['title'] ?></p>
                            </div>    
                            <?php
                        }
                    }
                }
                ?>
                <p class="customText" style="margin-top: 14px">
                    After you've selected your media kit items, the next step is to choose the specs for each product.
                </p>
                <input type="submit" value=" " name="print_form" class="go_button right"  style="margin-right: 14px; margin-top: 6px; border: none;">
            </form>
        </menu>
        <br class="clear"></div>
          <?php require_once APPPATH.'views/block/main_footer.php';?>

</div>