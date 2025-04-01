import os
import shutil
from subprocess import Popen

# Define paths
xampp_path = "C:/xampp"  # Update this path if your XAMPP installation is in a different location
mysql_data_path = os.path.join(xampp_path, "mysql", "data")
mysql_backup_path = os.path.join(xampp_path, "mysql", "backup")
mysql_data_old_path = os.path.join(xampp_path, "mysql", "data_old")

# Step 1: Rename mysql/data to mysql/data_old
if os.path.exists(mysql_data_path):
    print("Renaming mysql/data to mysql/data_old...")
    os.rename(mysql_data_path, mysql_data_old_path)
else:
    print("Error: mysql/data does not exist. Exiting.")
    exit(1)

# Step 2: Copy mysql/backup folder and rename it as mysql/data
if os.path.exists(mysql_backup_path):
    print("Copying mysql/backup to mysql/data...")
    shutil.copytree(mysql_backup_path, mysql_data_path)
else:
    print("Error: mysql/backup does not exist. Exiting.")
    exit(1)

# Step 3: Copy all database folders from mysql/data_old into mysql/data (excluding specific folders)
excluded_folders = {"mysql", "performance_schema", "phpmyadmin"}
print("Copying database folders from mysql/data_old to mysql/data...")
for folder in os.listdir(mysql_data_old_path):
    folder_path = os.path.join(mysql_data_old_path, folder)
    if os.path.isdir(folder_path) and folder not in excluded_folders:
        print(f"Copying folder: {folder}")
        shutil.copytree(folder_path, os.path.join(mysql_data_path, folder))

# Step 4: Copy ibdata1 file from mysql/data_old into mysql/data
ibdata1_old_path = os.path.join(mysql_data_old_path, "ibdata1")
ibdata1_new_path = os.path.join(mysql_data_path, "ibdata1")
if os.path.exists(ibdata1_old_path):
    print("Copying ibdata1 file...")
    shutil.copy(ibdata1_old_path, ibdata1_new_path)
else:
    print("Error: ibdata1 file does not exist in mysql/data_old. Exiting.")
    exit(1)

# Step 5: Start MySQL from XAMPP control panel
xampp_control_path = os.path.join(xampp_path, "xampp-control.exe")
if os.path.exists(xampp_control_path):
    print("Starting MySQL from XAMPP control panel...")
    Popen([xampp_control_path])  # Open XAMPP control panel
    # To start MySQL directly, use the following command instead:
    # Popen([os.path.join(xampp_path, "mysql_start.bat")])
else:
    print("Error: xampp-control.exe not found. Please start MySQL manually.")

print("Script completed successfully.")