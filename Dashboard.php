<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'PropertyManager.php';

$auth = new Auth($pdo);
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$propertyManager = new PropertyManager($pdo);
$properties = $propertyManager->getProperties($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Portfolio Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Property Portfolio</a>
            <div class="navbar-nav ml-auto">
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Property added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                There was an error adding the property. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div id="map" style="height: 400px;"></div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Portfolio Summary</h5>
                        <p>Total Properties: <?php echo count($properties); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h2>My Properties</h2>
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                    Add New Property
                </button>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Address</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Property Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($property['title']); ?></td>
                            <td><?php echo htmlspecialchars($property['address']); ?></td>
                            <td>$<?php echo number_format($property['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($property['status']); ?></td>
                            <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                            <td>
                                <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteProperty(<?php echo $property['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addPropertyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addPropertyForm" method="POST" action="add_property.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" id="searchAddress" class="form-control" name="address" required>
                        </div>

                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        <div class="mb-3">
                            <div id="propertyMap" style="height: 300px;"></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Available">Available</option>
                                    <option value="Rented">Rented</option>
                                    <option value="Sold">Sold</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Property Type</label>
                                <select class="form-select" name="property_type" required>
                                    <option value="House">House</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Land">Land</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addPropertyForm" class="btn btn-primary">Add Property</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function initMap() {
        try {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: {lat: -34.397, lng: 150.644}
            });

            <?php foreach ($properties as $property): ?>
            new google.maps.Marker({
                position: {
                    lat: <?php echo floatval($property['latitude']); ?>,
                    lng: <?php echo floatval($property['longitude']); ?>
                },
                map: map,
                title: "<?php echo addslashes($property['title']); ?>"
            });
            <?php endforeach; ?>
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    function initAutocomplete() {
        const input = document.getElementById('searchAddress');
        const autocomplete = new google.maps.places.Autocomplete(input);
        
        const propertyMap = new google.maps.Map(document.getElementById('propertyMap'), {
            center: { lat: -34.397, lng: 150.644 },
            zoom: 12
        });

        let marker = null;

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            
            if (!place.geometry) {
                return;
            }

            propertyMap.setCenter(place.geometry.location);
            propertyMap.setZoom(15);

            if (marker) {
                marker.setPosition(place.geometry.location);
            } else {
                marker = new google.maps.Marker({
                    map: propertyMap,
                    position: place.geometry.location,
                    draggable: true
                });
            }

            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();

            marker.addListener('dragend', function() {
                document.getElementById('latitude').value = marker.getPosition().lat();
                document.getElementById('longitude').value = marker.getPosition().lng();
            });
        });
    }

    function deleteProperty(propertyId) {
        if (confirm('Are you sure you want to delete this property?')) {
            window.location.href = `delete_property.php?id=${propertyId}`;
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        initAutocomplete();
    });
    </script>
</body>
</html>