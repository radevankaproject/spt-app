import os
import re

directory = '/www/spt-app/resources/views'

for root, dirs, files in os.walk(directory):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = content
            
            # Add defer to select2
            new_content = new_content.replace(
                '<script src="{{ asset(\'assets/vendor/libs/select2/select2.js\') }}"></script>',
                '<script src="{{ asset(\'assets/vendor/libs/select2/select2.js\') }}" defer></script>'
            )
            
            # Add defer to leaflet and markercluster
            new_content = new_content.replace(
                '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>',
                '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="" defer></script>'
            )
            new_content = new_content.replace(
                '<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin=""></script>',
                '<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin="" defer></script>'
            )
            
            # Add defer to apexcharts just in case
            new_content = new_content.replace(
                '<script src="{{ asset(\'assets/vendor/libs/apex-charts/apexcharts.js\') }}"></script>',
                '<script src="{{ asset(\'assets/vendor/libs/apex-charts/apexcharts.js\') }}" defer></script>'
            )

            # Add defer to sweetalert2
            new_content = new_content.replace(
                '<script src="{{ asset(\'assets/vendor/libs/sweetalert2/sweetalert2.js\') }}"></script>',
                '<script src="{{ asset(\'assets/vendor/libs/sweetalert2/sweetalert2.js\') }}" defer></script>'
            )
            
            # Fix Leaflet initialization to wait for DOMContentLoaded if it has $(function())
            # Usually Leaflet maps are initialized inside a $(function() {...}) block.
            
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print(f"Updated: {filepath}")

print("Done.")
