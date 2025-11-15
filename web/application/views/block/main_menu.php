<div id="contentNav" class="ContentNav">

    <div class="linkNavAll <?php if ($active_menu == 'Home') echo 'active'; ?>">
        <a href="/" id="Home"><img src="/images/<?php if ($active_menu == 'Home') echo 'new'; ?>Home.png"></a>
    </div>

    <div id="LinkNav1" class="linkNavAll <?php if ($active_menu == 'Design') echo 'active'; ?>">
        <a href="/design" style="text-decoration: none" ><b>DESIGN IT</b></a>
        <div id="designContent" class="submenu">
            <div class="blockrelative">
                <span class="navArrow"></span>  
            </div>
            <div class="bg_desingit_too">
                <div class="content_designit">
                    <div class="title_content_design">A Creative Agency at Your Fingertips</div>
                    <div class="desing_text">
                        InkRockit's parent company is a full-service creative agency, so let our rock star design team put the WOW into your project.
                    </div>


                    <ul class="menu_bar_desingit">
                        <li><img src="/images/point_desingit_menu.png"><a href="#">Print Design</a></li>
                        <li><img src="/images/point_desingit_menu.png"><a href="#">Brand Identity</a></li>
                        <li><img src="/images/point_desingit_menu.png"><a href="#">Websites</a></li>
                        <li><img src="/images/point_desingit_menu.png"><a href="#">Multi-media</a></li>
                        <li><img src="/images/point_desingit_menu.png"><a href="#">Packaging</a></li>
                        <li style="position: relative;"><img src="/images/point_desingit_menu.png"><a href="#">Trade Show<br> <span style="margin-left: 13px; position: absolute; top: 20px;">Graphics</span></a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>


    <div class="linkNavAll <?php if ($active_menu == 'Print') echo 'active'; ?>" id="LinkNav2">
        <a href="/print_it" style="text-decoration: none"><b>PRINT IT</b></a>
        <div id="printContent" class="submenu">
            <div class="blockrelative">
                <span class="navArrowprint"></span>  
            </div>
            <div class="content_printit">
                <div class="title_print_it">
                    Custom is Our Standard<br>
                    (but we do the simple stuff too)
                </div>

                <div class="printit_text">
                    If you need an item that isn't in the Product List below, give us a call and chances are that we will be able to provide you with a solution.
                </div>
                <div class="line_printit"></div>
                <ul class="menu_printit">
                    <li><a href="/WhyInckrokit">The InkRockit Difference</a></li>
                    <li class="product_hover">Product List<span class="product_list"></span>
                        <div style="width: 100px;position: absolute; height: 30px;right: -30px;top: 0px"></div>
                        <div class="submenu_product_list">
                            <ul class="printNavProdukt">
                                <?php
                                if (!empty($print_menu)) {
                                    foreach ($print_menu as $key => $val) {
                                        ?><li id="li<?= $key + 1 ?>"><a href="/print_it?get=<?= $val['id'] ?>"><?= $val['title'] ?></a></li><?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </li>
                    <li><a href="#">Product Templates</a></li>
                </ul>

            </div>

        </div>
    </div>


    <div id="LinkNav3" class="linkNavAll <?php if ($active_menu == 'Mail') echo 'active'; ?>">
        <a href="/mail" style="text-decoration: none;"><b>MAIL IT</b></a>
            <div id="mailContent" class="submenu">
                <div class="blockmail">
                    <span class="navArrowmail"></span>  
                </div>
                <div class="content_mailit">
                    <div class="title_mailit">Deliver on Time and on Target</div>
                    <div class="text_mailit">
                        Our effective direct mail capabilities will get your marketing materials to the right people at the right time.
                    </div>
                </div>
            </div>
    </div>



    <div id="LinkNav4" class="linkNavAll <?php if ($active_menu == 'WhyInckrokit') echo 'active'; ?>">
        <a href="/WhyInckrokit" style="text-decoration: none;"><b>Why inkRockit</b></a>
        <div id="InkContent" class="submenu">
            <div class="blockrelative">
                <span class="inkArrow"></span>  
            </div>
            <div class="content_inkroket">
                <div class="title_inkroket">Dedicated to Superior Quality & Service</div>
                <div class="text_inkroket">
                    From our services to our service, find out what it means to experience the InkRockit difference
                </div>
            </div>
        </div>
    </div>



    <div class="linkNavAll <?php if ($active_menu == 'inspiration') echo 'active'; ?>" id="LinkNav5">
        <a href="/inspiration_station/101"><b class="inspTextB">Inspiration Station</b></a>
        <div class="Babs"></div>

        <div id="InspirationContent" class="submenu">
            <div class="blockrelative">
                <span class="InspirationArrow"></span>  
            </div>
            <div class="top_block_ins">
                <div class="title_ins">A Gallery of Ideas to Ignite Your Imagination!</div>
                <div class="text_ins">
                    View samples of our work, complete with multiple photos, project specifications, templates, and the ability to order a product with the same specifications with the click of a button.
                </div>
            </div>
            <div class="content_inspirationS">

            </div>
        </div>
    </div>


    <div id="LinkNav6" class="linkNavAll <?php if ($active_menu == 'feel') echo 'active'; ?>">
        <b class="feel">Feel the</b>
        <img id="heart" src="/images/heart.png">
        <div id="fealContent" class="submenu">
            <div class="blockmail">
                <span class="navArrowfeal"></span>  
                <div class="content_feal">
                    <div class="title_feal">What Our Customers are Saying </div>
                    <div class="text_feal">
                        We do a pretty good job of touting our capabilities, but our customers are definitely our best sales people.
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="LinkNav7" class="linkNavAll <?php if ($active_menu == 'resources') echo 'active'; ?>">
        <b>Resources</b>
        <div id="ResContent" class="submenu">
            <div class="blockrelative">
                <span class="navArrowresours"></span>  
            </div>
            <ul class="ResNav">
                <li>Product Templates</li>
                <li>Ordering Process</li>
                <li>File Preparation</li>
                <li>Tips & Tools</li>
            </ul> 
        </div>
    </div>


    <div class="linkNavAll <?php if ($active_menu == 'contact') echo 'active'; ?>" id="LinkNav8">
        <b>Contact Us</b>
        <div id="ContactContent" class="submenu">
            <div class="blockrelative">
                <span class="navArrowContact"></span>  
            </div>
            <div class="phone_block">
                <span class="phone_title">800.900.5632</span> <span class="phone_description">(Toll Free)</span>
                <span class="phone_title">407.602.7202</span> <span class="phone_description">(Local U.S.)</span> 
            </div>
            <div class="contact_block_form">
                <div class="contact_title">Mailing Address</div>
                <div class="description_contacts">
                    InkRockit<br>
                    PO Box 951353<br>
                    Orlando, FL 32795-1353
                </div>
            </div>

            <div class="contact_block_form" style="margin-top: 5px;">
                <div class="contact_title">U.S. Facility</div>
                <div class="description_contacts">
                    2441 Bellevue Avenue Extension<br>
                    Daytona Beach, FL 32114
                </div>
            </div>

            <div class="contact_block_form">
                <div class="contact_title">Hong Kong Facility</div>
                <div class="description_contacts">
                    Block B, 1/F, Sin Hua Bank Building<br>
                    10-16 Luen Shing Street<br>
                    Fanling, HK
                </div>
            </div>
            <input class="bottom_contacts" type="button" name="MainFirst" value="Email Us" style="cursor: pointer; ">

        </div>
    </div>
</div>