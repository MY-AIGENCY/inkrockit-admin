<div class="mainContent" style="display: block; ">


    <div class="left">

        <aside class="mainHeader">
            <img src="/images/insp_info.png">
        </aside>

        <div class="mainLeftMenu">
        </div>


    </div>


    <div class="insp_gallery">

        <div class="gallery_large">
            <div class="hot_area" style="left: 0">
                <div class="go_prev">
                    <span class="arrow"></span>
                    <img src="/images/ButtonPlus.png">
                </div>
            </div>

            <div class="hot_area" style="right: 0">
                <div class="go_next">
                    <span class="arrow"></span>
                    <img src="/images/ButtonPlus.png">
                </div>
            </div>

            <img src="/images/admin/loading.gif" class="big">

        </div>
        <br class="clear">
        <div class="gallery_thumbs">
            <div class="prev_one"></div>
            <div class="thumb">
                <input type="hidden" name="active_element" value="<?= (empty($active)) ? intval($gallery[0]) : $active; ?>">
                <ul class="all_thumbs">
                    <?php
                    if (!empty($gallery)) {
                        $prev = 0;
                        foreach ($gallery as $key => $val) {
                            ?><li class="item_<?= $key ?>"><?php
                    foreach ($val as $v) {
                                ?>
                                    <span><img src="/files/items/thumbnails/<?= $v['img'] ?>.png" rel="/files/items/pics/<?= $v['img'] ?>.png"></span>
                                    <?php
                                }
                                ?></li><?php
                        }
                    }
                        ?>
                </ul>

            </div>
            <div class="next_one"></div>
        </div>
        <br class="clear">
    </div>




    <div class="MainTextBlock">
        Fusce nulla metus, porttitor at laoreet eget, eleifend sed nibh. Aliquam quis libero risus, non eleifend enim. Morbi eget arcu elit. Aenean mollis suscipit est ut pharetra. Integer libero dolor, tincidunt quis commodo vitae, mollis eget nunc! Phasellus sagittis sollicitudin ante, nec pulvinar sapien               
    </div> 
    <div class="comentBlock"><span class="comentName">Janice Joplin</span>
        <p class="comentWork">TCAC - Director of Marketing</p>
        <span class="comentHeart"><img src="/images/ComentHeart.png"></span>
    </div>
    <br class="clear">
</div>


<div class="modal">
    <img src="" style="max-width: 90%; max-height: 90%;">
</div>

<div class="next_preload hide"></div>