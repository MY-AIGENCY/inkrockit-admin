<div class="mainPage">
    <div class="content">

        <div class="left left_cont">
            <h1 class="printMainTitle"><?php
echo $order_titles[$orders[0]];
if (!empty($sub_order[1]))
    echo' + Stitched-in Booklet';
?>
                <div class="text-gradient"></div>
            </h1>
            <div id="print_big_preview">
                <?php if (!empty($items)) { ?>
                    <img src="/files/print/view/<?php
                $active = (count($items) > 1) ? 1 : 0;
                echo $items[$active]['id'];
                    ?>.jpg"><?php } ?>
            </div>

            <p class="center" style="padding-top: 6px"><span class="print_page_descr">Start Here:</span> <span class="print_page_descr2">Select You Folder Style</span></p>
            <br>            
            <div class="pring_gall_prew">
                <div class="button"></div>
            </div>
            <div class="pring_gall_previews">
                <?php
                if (!empty($items)) {
                    foreach ($items as $key => $val) {
                        ?><div class="item_prew_block <?php if ($key == $active) echo'active'; ?>" style="left: <?= ($key * 148) + 8 ?>px;" data-id="<?= $val['id'] ?>">
                            <div class="print_preview" style="background: url(/files/print/<?php if ($key == $active) echo'active_'; ?>preview/<?= $val['id'] ?>.jpg) no-repeat bottom;"></div>
                            <div class="center"><?= $val['abbr'] ?></div>
                        </div><?php
            }
        }
                ?>
            </div>
            <div class="pring_gall_next">
                <div class="button"></div>
            </div>

            <br class="clear"><br>
            <div class="col300 marg_t10">
                Urna tristique ac aliquet adipiscing habitasse ridiculus sed, dapibus est tincidunt habitasse amet turpis, nisi lorem magnis tincidunt, integer magnis, scelerisque mattis lectus pulvinar
            </div>
            <div class="col300 marg_t10">
                Dictumst, pid augue pid, vut, mus sociis in mauris, auctor, mid enim. Adipiscing, elit in magnis et est non mus enim integer velit, elementum nisi sit in porttitor arcu elementum magnis! 
            </div>
        </div>


        <menu class="mainRightMenu" style="width: 324px">
            <div class="custom">
                <div class="helvetica f25 color_pink marg_b5">MEDIA KIT ITEMS</div>
                <span class="font_creighton f27">Customize Your Content</span>
            </div>
            <p class="customText">
                To get pricing, select the specs you want for each product. The standard selections have been entered as default specs. 
                If you need help or have a special request, please give us a call at 1-800-900-5632.
            </p>

            <div>
                <?php
                foreach ($orders as $val) {
                    ?>
                    <div class="mainLeftMenuTop">
                        <span class="open_status"></span>
                        <span class="title">
                            <?=
                            $order_titles[$val];
                            if ($val != 1) {
                                ?><img src="/images/rem.png" class="remove"><?php
                    }
                    if (!empty($sub_order[$val])) {
                        echo ' + Stitched-in Booklet';
                    }?>
                        </span>
                        <div class="more_info">
                            <img src="/images/admin/loading.gif" style="margin: 10px auto; display: block;">                           
                        </div>
                    </div>
                    <?php
                }
                ?>
                <br class="clear">
                <small style="margin-left: 30px; margin-top: 15px; margin-bottom: 5px; display: block; font-size: 12px">
                    <form action="/print_it" method="post">
                        <strong>Add an item to you media kit</strong>
                        <?php
                        if ($orders) {
                            foreach ($orders as $val) {
                                ?><input type="hidden" name="order[]" value="<?= $val ?>"><?php
                    }
                    if (!empty($sub_order)) {
                        foreach ($sub_order as $val) {
                                    ?><input type="hidden" name="sub_order[]" value="<?= $val ?>"><?php
                    }
                }
            }
                        ?>
                        <input type="submit" value="Click Here" class="button">
                    </form>
                </small>
            </div>
        </menu>
        <br class="clear">
    </div>
</div>

<script src="/js/actual.js"></script>
<script>
    /* fix sub_ul height */
    $(function(){
        $.each($('.sub_ul'),function(){
            var heightArray = $(this).find('dt').map( function(){
                return $(this).actual('height');
            }).get();
            var maxHeight = Math.max.apply( Math, heightArray);
            $(this).find('dt').map(function(){
                $(this).css('minHeight',maxHeight+145);
            });
        });
    });
</script>