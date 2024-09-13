#!/bin/bash

# Navigate to the script's directory
cd "${0%/*}"

# Activate the virtual environment
source venv/bin/activate

# Run the Python script with the output file argument
python export.py "$1"

# Deactivate the virtual environment
deactivate
