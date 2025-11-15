<div id="activity_stats">
    <h3>Product Properties</h3>
</div>

<div class="ui-tabs ui-widget ui-widget-content ui-corner-all">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <?php 
        $active = strtolower($active);
        $active_class = ' ui-tabs-selected ui-state-active';
        ?>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/category')!=FALSE) echo $active_class; ?>"><a href="/admin/Print/category">Product Type</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/folding')!=FALSE) echo $active_class;?>"><a href="/admin/Print/folding">Folding</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/pockets')!=FALSE) echo $active_class;?>"><a href="/admin/Print/pockets">Pockets</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/slits')!=FALSE) echo $active_class;?>"><a href="/admin/Print/slits">BC/CD Slits</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/papers')!=FALSE) echo $active_class;?>"><a href="/admin/Print/papers">Papers</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/inks')!=FALSE) echo $active_class;?>"><a href="/admin/Print/inks">INKS</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/coating')!=FALSE) echo $active_class;?>"><a href="/admin/Print/coating">Coating</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/finishes')!=FALSE) echo $active_class;?>"><a href="/admin/Print/finishes">Finishes</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/proof')!=FALSE) echo $active_class;?>"><a href="/admin/Print/proof">Proof</a></li>
        <li class="ui-state-default ui-corner-top <?php if(strpos($active,'print/shipping')!=FALSE) echo $active_class;?>"><a href="/admin/Print/shipping">Shipping</a></li>
    </ul>
    <div class="ui-tabs-panel ui-widget-content ui-corner-bottom">
        <?= $content; ?>
    </div>
</div>
<script>
$(function(){
    $.each($('#secondary_nav li'), function(){
        if($(this).find('a').text()=='Product Properties'){
            $(this).addClass('checked');
        }
    });
});
</script>