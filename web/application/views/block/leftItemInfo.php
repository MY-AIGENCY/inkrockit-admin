
            <span class="mainLeftMenuTitle">Media Kit</span>
            <span class="mainLeftMenuText"><?=$type_title?></span>

            <?php
            foreach($blocks as $block){
            ?>
                <div class="mainLeftMenuTop">
                    <span class="open_status"></span>
                    <span class="title"><?=$block['title']?></span>
                    <div class="more_info">
                        <p style="padding: 5px;"><?=$block['text']?></p>
                        <ul class="insp_more_bock">
                            <?php 
                            foreach($block['child_blocks'] as $child) { ?>
                            <li>
                                <div class="title"><span class="open_status"></span><?=$child['title']?></div>
                                <div class="more_block_content">
                                    <hr>
                                    <?php
                                    $text = explode('<br />', nl2br($child['text']));
                                    foreach($text as $val){
                                        if(strpos($val, ':')){
                                            $elems = explode(':', $val);
                                            echo '<b>'.$elems[0].':</b>'.$elems[1].'<br>';
                                        }else{
                                            echo $val.'<br>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php
            }
            ?>
            
            
            <br class="clear">


            <div class="insp_download">
                <div class="left">
                    <strong>Template Downloads</strong><br>
                    <span class="comments">Select a file format</span>
                </div>
                <div class="right">
                    <img src="/images/DownloadAi.png">
                    <img src="/images/DownloadPs.png">
                    <img src="/images/DownloadId.png">
                    <img src="/images/DownloadPDF.png">
                </div>
                <br class="clear">
            </div>

            <img src="/images/edit.png" class="left button">
            <img src="/images/add_cart.png" class="right button">
            <br class="clear">