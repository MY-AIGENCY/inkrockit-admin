<div id="activity_stats">
    <h3>Products</h3>
</div>

<a class="button_big" href="/admin/Print/items/add"><span class="iconsweet">+</span>Add New Product</a>
<a class="button_big" href="/admin/Print/category"><span class="iconsweet">|</span>Edit Product Properties</a>

<div class="one_wrap">

    <div class="widget" style="width: 800px">
        <div class="widget_body">
            <?php if (!empty($category)) { ?>
                <table class="activity_datatable" width="800" id="sortTable">
                    <tbody>
                        <?php
                        foreach ($category as $val) {
                            ?><tr>
                                <td>
                                    <a href="/admin/Print/items/<?= $val['id'] ?>"><?= $val['title'] ?></a>
                                </td>
                            </tr><?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php } else {
                ?>
                <em>*No category</em>    
                <?php }
            ?>
        </div>
    </div>
</div>