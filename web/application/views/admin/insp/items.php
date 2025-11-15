<div id="activity_stats">
    <h3>Sample Inventory</h3>
</div>

<div class="one_wrap">

    <div class="h50">
        <ul class="form_fields_container search_block">
            <li>
                <input type="checkbox" name="check_all" class="left hide" style="margin: 8px 8px 8px 24px">

                <div class="adv_menu left">
                    <div class="right pointer" id="adv_search_show" style="padding: 10px">&blacktriangledown; Advanced Search</div>
                    <dl class="search_menu hide">
                        <dt><input type="checkbox" name="insp_field1" value="items" class="pointer" checked="checked"> Title</dt>
                        <dt><input type="checkbox" name="insp_field2" value="types" class="pointer" checked="checked"> Industries</dt>
                        <dt><input type="checkbox" name="insp_field3" value="cats"  class="pointer" checked="checked"> Category</dt>
                        <dt><input type="checkbox" name="insp_field4" value="item_id"  class="pointer" checked="checked"> Index</dt>
                        <dt><input type="checkbox" name="insp_field5" value="job_id"  class="pointer" checked="checked"> Job #</dt>
                    </dl>
                </div>

                <div class="form_input">
                    <label>Search:</label> 
                    <input type="text" name="search_insp" class="search_field left">
                </div>

                <div style="margin-right: 10px; text-align: right" class="buttons_block">
                    <a href="/admin/Inspiration/items/add" class="dblueBtn button_small"><span class="iconsweet">+</span> Add new Item</a>
                    <?php
                    if (!empty($empty_items)) {
                        ?><a href="/admin/Inspiration/items/fill" class="dblueBtn button_small">Empty Items (<?= $empty_items ?>)</a><?php
                    }
                    ?>
                    <div class="edit_insp_sent hide right"><a class="dblueBtn button_small"> <span class="iconsweet">8</span> Edit</a></div>
                    <div class="rem_insp_sent hide right"><a class="dblueBtn button_small"> <span class="iconsweet">X</span> Remove</a></div>
                </div>

            </li>
        </ul>
    </div>

    <div class="widget" style="width: 100%">
        <div class="widget_body">
            <table class="activity_datatable" width="100%" id="sortTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="check_all"></th>
                        <th>Index</th>
                        <th>QTY</th>
                        <th>Job #</th>
                        <th>Image</th>
                        <th>Product Type</th>
                        <th>Sample Name</th>
                        <th>Industry</th>
                        <th>Special Finishes</th>
                        <th>Client Item</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($items['items'] as $item) {
                        ?>
                        <tr>
                            <td width="30" class="center"><input type="checkbox" name="edit_insp" data-check="all" value="<?= $item['item_id'] ?>" data-id="<?= $item['id'] ?>"></td>
                            <td width="40"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['item_id'] ?></a></td>
                            <td width="30"><?= $item['count'] ?></td>
                            <td width="70"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['job_id'] ?></a></td>
                            <td width="90"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>">
                                    <img src="/files/items/thumbnails/<?= $item['item_id'] ?>.png" style="max-width: 90px">
                                </a></td>
                            <td width="100"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?=
                                    $item['cat_abbr'];
                                    if (!empty($items['sizes'][$item['item_id']])) {
                                        echo ' ' . $items['sizes'][$item['item_id']];
                                    }
                                    ?></a></td>
                            <td><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['title'] ?></a></td>
                            <td width="170"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['type_title'] ?></a></td>
                            <td width="200"><a style="font-size: 10px" href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['finish_line'] ?></a></td>
                            <td width="70"><?= ($item['client_item']) ? 'Yes' : 'No' ?></td>
                            <td width="50"><?= ($item['active']) ? 'Yes' : 'No' ?></td>
                            <td width="50">
                                <a class="ui-icon ui-icon-pencil left" href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>">edit</a>
                                <a class="ui-icon ui-icon-closethick left" original-title="Delete" href="/admin/inspiration/items/remove/<?= $item['id'] ?>">del</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <?php if (!empty($paginator)) {
                ?><div class="content_pad text_center">
                    <ul class="pagination"><?php
                        echo $paginator;
                        ?></ul></div><?php }
                    ?>

        </div>
    </div>
</div>

<script>
    $(function() {
        $("#sortTable").tablesorter({headers: {
                0: {sorter: false},
                4: {sorter: false},
                10: {sorter: false}
            }});
    })
</script>