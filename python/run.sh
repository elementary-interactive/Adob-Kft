cd "${0%/*}"
source venv/bin/activate
python export.py
deactivate
