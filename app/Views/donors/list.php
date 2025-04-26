<!DOCTYPE html>
<html>

<head>
    <title>Donors List</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container">
        <div class="row d-flex align-items-center my-3">
            <!-- Blood Group Filter -->
            <div class="col-auto">
                <select id="filter_blood" class="form-select">
                    <option value="">All Blood Groups</option>
                    <option value="A+">A+</option>
                    <option value="B+">B+</option>
                    <option value="O+">O+</option>
                    <!-- Add all blood groups here -->
                </select>
            </div>

            <!-- District Filter -->
            <div class="col-auto">
                <select id="filter_district" class="form-select">
                    <!-- District options will be populated dynamically -->
                </select>
            </div>

            <!-- Thana Filter -->
            <div class="col-auto">
                <select id="filter_thana" class="form-select">
                    <!-- Thana options will be populated dynamically -->
                </select>
            </div>

            <!-- Last Donate Filter -->
            <div class="col-auto">
                <select id="filter_last_donate" class="form-select">
                    <option value="">Any Donation Date</option>
                    <option value="1">1 Month Ago</option>
                    <option value="2">2 Months Ago</option>
                    <option value="3">3 Months Ago</option>
                    <option value="6">6 Months Ago</option>
                    <option value="12">12 Months Ago</option>
                    <option value="more">More than 1 Year</option>
                </select>
            </div>

            <!-- Add Donors Button -->
            <div class="col-auto">
                <button id="add-donors" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalId">Add Donors</button>
            </div>
        </div>

        <table id="donorTable" class="display mt-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Blood_group</th>
                    <th>District</th>
                    <th>Thana</th>
                    <th>Last donate</th>
                </tr>
            </thead>
        </table>

        <div
            class="modal fade"
            id="modalId"
            tabindex="-1"
            data-bs-backdrop="static"
            data-bs-keyboard="false"

            role="dialog"
            aria-labelledby="modalTitleId"
            aria-hidden="true">
            <div
                class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Modal title
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="donorForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select id="blood_group" class="form-select" name="blood_group">
                                    <option value="">All Blood Groups</option>
                                    <option value="A+">A+</option>
                                    <option value="B+">B+</option>
                                    <option value="O+">O+</option>
                                    <!-- Add all -->
                                </select>
                            </div>


                            <div class="mb-3">
                                <label for="district" class="form-label">District</label>
                                <select class="form-select" id="form-district" name="district">
                                    <option value="">Select District</option>
                                    <option value="Dhaka">Dhaka</option>
                                    <option value="Chattogram">Chattogram</option>
                                    <option value="Rajshahi">Rajshahi</option>
                                    <option value="Khulna">Khulna</option>
                                    <option value="Barishal">Barishal</option>
                                    <option value="Sylhet">Sylhet</option>
                                    <option value="Rangpur">Rangpur</option>
                                    <option value="Mymensingh">Mymensingh</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Thana</label>
                                <select class="form-select" id="form-thana" name="thana">
                                    <option value="">Select Thana</option>
                                    <!-- Thanas will be populated here based on selected district -->
                                </select>

                            </div>

                            <div class="mb-3">
                                <label for="last_donate" class="form-label">Last Donate</label>
                                <input type="date" class="form-control" id="last_donate" name="last_donate">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" id="saveDonorBtn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function loadDistrictOptions() {
            $.get('/donors/getDistricts', function(data) {
                let html = '<option value="">Select District</option>';
                data.forEach(d => {
                    html += `<option value="${d.district}">${d.district}</option>`;
                });
                $('#district').html(html);
            });
        }

        function loadThanaOptions(district) {
            $.post('/donors/getThanas', {
                district: district
            }, function(data) {
                let html = '<option value="">Select Thana</option>';
                data.forEach(t => {
                    html += `<option value="${t.thana}">${t.thana}</option>`;
                });
                $('#thana').html(html);
            });
        }

        // Trigger when modal is shown
        $('#modalId').on('shown.bs.modal', function() {
            loadDistrictOptions();
            $('#thana').html('<option value="">Select Thana</option>'); // reset thana
        });

        // Update thanas when district changes

        $('#form-district').change(function() {
            var district = $(this).val();
            console.log(district);

            if (district) {
                $.ajax({
                    url: '/donors/getThanas',
                    type: 'POST',
                    data: {
                        district: district
                    },
                    dataType: 'json',
                    success: function(data) {
                        var thanaOptions = '<option value="">Select Thana</option>';
                        $.each(data, function(index, thana) {
                            thanaOptions += '<option value="' + thana + '">' + thana + '</option>';
                        });
                        $('#form-thana').html(thanaOptions);
                    }
                });
            } else {
                $('#form-thana').html('<option value="">Select Thana</option>');
            }
        });


        $('#filter_district').change(function() {
            const selectedDistrict = $(this).val();
            if (selectedDistrict) {
                loadThanaOptions(selectedDistrict);
            } else {
                $('#thana').html('<option value="">Select Thana</option>');
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#saveDonorBtn').click(function() {
                var formData = $('#donorForm').serialize();

                $.ajax({
                    url: '/donors/create',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#modalId').modal('hide');
                            $('#donorForm')[0].reset();
                            $('#donorTable').DataTable().ajax.reload();

                            // alert('Donor saved successfully!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong while saving.');
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            loadDistricts();

            var table = $('#donorTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "/donors/datatable",
                    "type": "POST",
                    "data": function(d) {
                        d.blood_group = $('#filter_blood').val();
                        d.district = $('#filter_district').val();
                        d.thana = $('#filter_thana').val();
                        d.last_donate = $('#filter_last_donate').val();
                    }
                },
                "columns": [{
                        "data": "name"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "blood_group"
                    },
                    {
                        "data": "district"
                    },
                    {
                        "data": "thana"
                    },
                    {
                        "data": "last_donate"
                    }
                ]
            });

            $('#filter_blood, #filter_district, #filter_thana').change(function() {
                table.draw();
            });

            function loadDistricts() {
                $.get('/donors/getDistricts', function(data) {
                    let html = '<option value="">All Districts</option>';
                    data.forEach(d => {
                        html += `<option value="${d.district}">${d.district}</option>`;
                    });
                    $('#filter_district').html(html);
                });
            }

            $('#filter_district').change(function() {
                $.post('/donors/getFilterThanas', {
                    district: $(this).val()
                }, function(data) {
                    let html = '<option value="">All Thanas</option>';
                    data.forEach(t => {
                        html += `<option value="${t.thana}">${t.thana}</option>`;
                    });
                    $('#filter_thana').html(html);
                });
            });


            $('#filter_last_donate').change(function() {
                table.draw();
            });

        });
    </script>

</body>

</html>