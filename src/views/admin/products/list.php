<!DOCTYPE html>
<html lang="en">
<?php renderFile('includes.admin.head', ['title' => 'لیست محصولات']) ?>

<body>
    <div class="wrapper">
        <?php renderFile("includes.admin.sidebar") ?>
        <div class="main">
            <?php renderFile("includes.admin.header") ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-3"> Products list</h1>
                        <a class="btn btn-outline-success">New product <i data-feather="plus"></i></a>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <div class="card flex-fill">
                                <table class="table table-hover my-0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th class="d-none d-xl-table-cell">Price</th>
                                            <th class="d-none d-xl-table-cell">Quantity</th>
                                            <th>Status</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $key => $item): ?>
                                            <tr>
                                                <td><?php echo $item['title'] ?></td>
                                                <td class="d-none d-xl-table-cell"><?php echo number_format($item['price']) ?></td>
                                                <td class="d-none d-xl-table-cell"><?php echo number_format($item['quantity']) ?></td>
                                                <td><span class="badge bg-<?php echo $item['status'] === 'published' ? 'success' : 'danger' ?>"><?php echo $item['status']; ?></span></td>
                                                <td class="d-none d-md-table-cell">
                                                    <a class="btn btn-outline-primary" href="">Edit <i class="align-middle" data-feather="edit"></i></a>
                                                    <button class="btn btn-outline-danger">Delete <i class="align-middle" data-feather="trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php renderFile("includes.admin.footer") ?>
        </div>
    </div>
    <?php renderFile('includes.admin.foot') ?>
</body>

</html>