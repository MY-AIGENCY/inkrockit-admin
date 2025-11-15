<a class="button_big" href="<?= $_SERVER['REQUEST_URI'] ?>/add"><span class="iconsweet">+</span>Add new Paper</a>

<div class="one_wrap">

    <div class="widget" style="width: 800px">
        <div class="widget_body">
            <table class="activity_datatable" width="800" id="sortTable">
                <thead> 
                    <tr>
                        <th>Title</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($papers)) {
                        foreach ($papers as $val) {
                            ?>
                            <tr>
                                <td><a href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $val['id'] ?>"><?= $val['name'] ?></a></td>
                                <td width="80">
                                    <a class="ui-icon ui-icon-pencil left" href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $val['id'] ?>">edit</a>
                                    <a class="ui-icon ui-icon-closethick left" original-title="Delete" href="<?= $_SERVER['REQUEST_URI'] ?>/del/<?= $val['id'] ?>">del</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function(){
        $("#sortTable").tablesorter({headers: { 
                2: { sorter: false }
            }}); 
    })
</script>