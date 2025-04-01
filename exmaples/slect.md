
$sql = "SELECT * FROM menu";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['menu_id'] . "</td>";
        echo "<td><img src='../images/". $row['image']. "' alt='". $row['name']. "' width='50'></td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['category'] . "</td>";
        echo "<td>$" . number_format($row['price'], 2) . "</td>";
        echo "<td>
                <button class='btn btn-sm btn-primary edit-btn' 
                    data-id='" . $row['menu_id'] . "'
                    data-name='" . $row['name'] . "'
                    data-description='" . $row['description'] . "'
                    data-price='" . $row['price'] . "'
                    data-category='" . $row['category'] . "'>
                    <i class='fas fa-edit'></i>
                </button>
                <button class='btn btn-sm btn-danger delete-btn' data-id='" . $row['menu_id'] . "'>
                    <i class='fas fa-trash'></i>
                </button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No menu items found</td></tr>";
}
?>