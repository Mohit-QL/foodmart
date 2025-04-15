<?php
session_start();
$conn = new mysqli("localhost", "root", "", "foodmart");

if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit;
}

$categories_result = $conn->query("SELECT * FROM categories");

$uploaded_images = []; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $old_price = $_POST['old_price'];
    $product_size = $_POST['product_size'];
    $user_id = $_SESSION['user_id'];

    $image_paths = [];
    if (isset($_FILES['product_images']) && count($_FILES['product_images']['name']) > 0) {
        $total_images = count($_FILES['product_images']['name']);
        if ($total_images >= 1 && $total_images <= 5) {
            for ($i = 0; $i < $total_images; $i++) {
                $image_tmp_name = $_FILES['product_images']['tmp_name'][$i];
                $image_name = basename($_FILES['product_images']['name'][$i]);
                $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

                if (in_array($image_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $image_new_name = uniqid('product_', true) . '.' . $image_extension;
                    $image_upload_path = 'uploads/products/' . $image_new_name;

                    if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                        $image_paths[] = $image_upload_path;
                        $uploaded_images[] = $image_name; 
                    } else {
                        echo "Error uploading image.";
                        exit;
                    }
                } else {
                    echo "Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.";
                    exit;
                }
            }
        } else {
            echo "Please upload between 1 and 5 images.";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_price, old_price, product_size, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddssi", $product_name, $product_description, $product_price, $old_price, $product_size, $user_id, $_POST['category_id']);
    $stmt->execute();

    $product_id = $conn->insert_id;

    foreach ($image_paths as $path) {
        $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $path);
        $stmt->execute();
    }

    header("Location: products.php");
    exit;
}
?>

<?php include './header.php'; ?>
<div class="container py-4">
    <h2 class="mb-4">Add New Product</h2>
    <form method="POST" action="add-product.php" enctype="multipart/form-data" class="border p-4 bg-light rounded">
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="product_description" class="form-label">Description</label>
            <textarea name="product_description" id="product_description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="product_price" class="form-label">Price</label>
            <input type="number" name="product_price" id="product_price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="old_price" class="form-label">Old Price</label>
            <input type="number" name="old_price" id="old_price" class="form-control" step="0.01">
        </div>
        <div class="mb-3">
            <label for="product_size" class="form-label">Size</label>
            <input type="text" name="product_size" id="product_size" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="product_images" class="form-label">Product Images (Max 5 images)</label>
            <input type="file" name="product_images[]" id="product_images" class="form-control" accept="image/*" multiple onchange="updateImageNames()">
        </div>
        <div id="image_names"></div> 
        <button type="submit" class="btn btn-success">Add Product</button>
    </form>

    <a href="products.php" class="btn btn-link ps-4 pt-3 text-success">Back to Products</a>
</div>
<?php include './footer.php'; ?>

<script>
    function updateImageNames() {
        const input = document.getElementById('product_images');
        const imageNamesContainer = document.getElementById('image_names');
        const files = input.files;
        const maxFiles = 5;
        
        if (files.length > maxFiles) {
            alert("You can upload a maximum of 5 images.");
            return;
        }

        // Clear previous image names
        imageNamesContainer.innerHTML = '';

        // Display image names
        for (let i = 0; i < files.length; i++) {
            const li = document.createElement('li');
            li.textContent = files[i].name;
            imageNamesContainer.appendChild(li);
        }
    }
</script>
