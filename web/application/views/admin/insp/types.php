<div id="activity_stats">
    <h3>Industries</h3>
</div>


<a class="button_big" href="<?= $_SERVER['REQUEST_URI'] ?>/add"><span class="iconsweet">+</span>Add new Industries</a>

<div class="one_wrap">

    <div class="widget" style="width: 800px">
        <div class="widget_body">
            <table class="activity_datatable" width="800" id="sortTable">
                <thead>
                    <tr>
                        <th>Index</th>
                        <th>Title</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($types as $type) {
                        ?>
                        <tr>
                            <td width="40"><?= $type['index'] ?></td>
                            <td><a href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $type['id'] ?>"><?= $type['title'] ?></a></td>
                            <td width="50"><?= ($type['active']) ? 'Yes' : 'No' ?></td>
                            <td width="50">
                                <a class="ui-icon ui-icon-pencil left" href="<?= $_SERVER['REQUEST_URI'] ?>/edit/<?= $type['id'] ?>">edit</a>
                                <a class="ui-icon ui-icon-closethick left" original-title="Delete" href="<?= $_SERVER['REQUEST_URI'] ?>/del/<?= $type['id'] ?>">del</a>
                            </td>
                        </tr>
                        <?php
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
                3: { sorter: false }
            }}); 
    })
</script>