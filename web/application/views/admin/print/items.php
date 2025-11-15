<div id="activity_stats">
    <h3>Products &raquo; <?=$category['title']?></h3>
</div>

<a class="button_big" href="/admin/Print/items/add"><span class="iconsweet">+</span>Add New Product</a>
<a class="button_big" href="/admin/Print/category"><span class="iconsweet">|</span>Edit Product Properties</a>

<div class="one_wrap">
    <?php if (!empty($items)) { ?>
    
        <div class="widget" style="width: 800px">
            <div class="widget_body">          
                <table class="activity_datatable" width="800" id="sortTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($items as $item) {
                            ?>
                            <tr>
                                <td><a href="/admin/Print/items/edit/<?= $item['id'] ?>"><?= $item['title'] ?></a></td>
                                <td><?= $item['category'] ?></td>
                                <td width="50"><?= ($item['active']) ? 'Yes' : 'No' ?></td>
                                <td width="50">
                                    <a class="ui-icon ui-icon-pencil left" href="/admin/Print/items/edit/<?= $item['id'] ?>">edit</a>
                                    <a class="ui-icon ui-icon-closethick left" href="/admin/Print/items/del/<?= $item['id'] ?>">del</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    
    <?php } else {
        ?><br><em>*No Items</em><br><?php }
    ?>
    <br>
    <a href="/admin/Print/items/">&laquo; back</a>
    <br>
</div>

<script>
    $(function() {
        $("#sortTable").tablesorter({headers: {
                3: {sorter: false}
            }});
    })
</script>