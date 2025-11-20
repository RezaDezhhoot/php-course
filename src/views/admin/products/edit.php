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
                        <h1 class="h3 mb-3"> Products edit <?php echo $product['title'] ?></h1>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <div class="card flex-fill">
                                <form action="/admin/products/edit/<?php echo $product['id'] ?>" method="post" enctype="multipart/form-data">
                                    <div class="row p-4">
                                        <div class="form-group col col-md-6">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input required type="text" value="<?php echo $product['title'] ?>" class="form-control" id="title" name="title" placeholder="Enter title">
                                            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                                        </div>
                                        <div class="form-group col col-md-6">
                                            <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                            <input required value="<?php echo $product['quantity'] ?>" type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                                        </div>
                                        <div class="form-group col col-md-6">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            <select required class="form-control" name="status" id="status">
                                                <option value="">Select</option>
                                                <option <?php echo $product['status'] === 'published' ? 'selected' : '' ?> value="published">منتشر شده</option>
                                                <option <?php echo $product['status'] === 'draft' ? 'selected' : '' ?> value="draft">پیشنویس</option>
                                            </select>
                                        </div>
                                        <div class="form-group col col-md-6">
                                            <label for="price">Price <span class="text-danger">*</span></label>
                                            <input value="<?php echo $product['price'] ?>" required type="number" class="form-control" id="price" name="price" placeholder="Enter price">
                                            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                                        </div>
                                        <div class="form-group col-12">
                                            <input multiple type="file" accept="image/png, image/jpeg, image/gif" name="images[]" class="custom-control-input" id="images">
                                            <label class="custom-control-label" for="images">Choose file</label>
                                            <?php if ($product['images']): ?>
                                                <?php foreach (json_decode($product['images']) as $img): ?>
                                                    <img src="<?php echo $img ?>" style="width: 50px;height:50px" alt="" srcset="">
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group col-12">
                                            <label for="body">Body</label>
                                            <textarea name="body" class="form-control" id="body" rows="3"><?php echo $product['body'] ?></textarea>
                                        </div>
                                        <div class="form-group col-12 text-center mt-4">
                                            <button class="btn mx-auto btn-outline-success" type="submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
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