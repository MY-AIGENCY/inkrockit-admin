<?php
if (!empty($items)) {
    ?>
    <table class="activity_datatable" width="100%" id="sortTable">
        <thead>
            <tr>
                <th><input type="checkbox" name="check_all"></th>
                <th>Index</th>
                <th>QTY</th>
                <th>Job #</th>
                <th>Image</th>
                <th>Industry</th>
                <th>Sample Name</th>
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
                    <td width="90"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['job_id'] ?></a></td>
                    <td width="90"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>">
                            <img src="/files/items/thumbnails/<?= $item['item_id'] ?>.png" style="max-width: 90px">
                        </a></td>
                    <td width="120"><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?=
                            $item['cat_abbr'];
                            if (!empty($items['sizes'][$item['item_id']])) {
                                echo ' ' . $items['sizes'][$item['item_id']];
                            }
                            ?></a></td>
                    <td><a href="/admin/inspiration/items/edit/<?= $item['item_id'] ?>"><?= $item['title'] ?></a></td>
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

<?php } else {
    ?><p style="padding: 20px">*Nothing found</p><?php
}
?>