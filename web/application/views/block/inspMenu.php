<?php
$i=4;
foreach($inspMenu as $menu_item){
    ?>
        <div id="content<?=$i?>" class="InspirationArtikle">
                    <span class="Insp"><img id="content<?=$i?>" src="/images/insphide.png"></span>
                    <p class="inspText"><?=$menu_item['title']?></p>
                </div>    
        <div name="content<?=$i?>" class="inspcontent">
                    <div class="leftMenuArrowTop"><span class="menuLeftArrow"></span></div> 
                    <div class="rightMenuArrowTop"><span class="menuRightArrow"></span></div> 
                    <div class="separatorImg">

                        <div class="moreItem">
                            <?php foreach($menu_item['items'] as $key => $val) {
                                ?>
                                <div id="imgRel">
                                    <a href="/inspiration_station/<?=$key?>" style="background:url('/files/items/thumbnails/<?=$val['img']; ?>.png') no-repeat center center; width:80px; margin-left: 2px;height:100px;float:left;"></a>
                                    <?php
                                    if($val['client_item'] == 0){
                                        ?><div class="dopinsp"></div><?php
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <br class="clear">
                </div>
    <?php
    $i++;
}
?>