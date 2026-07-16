const fs = require('fs');
let file = fs.readFileSync('/www/spt-app/resources/views/admin/jukirs/index.blade.php', 'utf8');

const jsLogic = `
        // Handle Dynamic Dropdown for Create Modal
        const createAllParkingOptions = $('#create_parking_location_id option').clone();
        $('#create_road_section_id').on('change', function() {
            let roadSectionId = $(this).val();
            let parkingSelect = $('#create_parking_location_id');
            parkingSelect.empty();
            parkingSelect.append('<option value="">Pilih Titik Parkir</option>');
            if (roadSectionId) {
                createAllParkingOptions.each(function() {
                    if ($(this).val() == '' || $(this).data('road-section-id') == roadSectionId) {
                        parkingSelect.append($(this).clone());
                    }
                });
                parkingSelect.prop('disabled', false);
            } else {
                parkingSelect.prop('disabled', true);
            }
            parkingSelect.trigger('change');
        });

        // Handle Dynamic Dropdown for Edit Modal
        window.editAllParkingOptions = $('#edit_parking_location_id option').clone();
        $('#edit_road_section_id').on('change', function(e, isInit) {
            let roadSectionId = $(this).val();
            let parkingSelect = $('#edit_parking_location_id');
            let currentValue = parkingSelect.val();
            parkingSelect.empty();
            parkingSelect.append('<option value="">Pilih Titik Parkir</option>');
            if (roadSectionId) {
                window.editAllParkingOptions.each(function() {
                    if ($(this).val() == '' || $(this).data('road-section-id') == roadSectionId) {
                        parkingSelect.append($(this).clone());
                    }
                });
                parkingSelect.prop('disabled', false);
            } else {
                parkingSelect.prop('disabled', true);
            }
            if (isInit) {
                parkingSelect.val(currentValue);
            } else {
                parkingSelect.val('');
            }
            parkingSelect.trigger('change');
        });
`;

if (!file.includes('createAllParkingOptions')) {
    file = file.replace('$(document).ready(function() {', '$(document).ready(function() {' + jsLogic);
    fs.writeFileSync('/www/spt-app/resources/views/admin/jukirs/index.blade.php', file);
    console.log("Appended!");
}
