import os
import re

directories = [
    r"c:\xampp\htdocs\myfirstlaravel\resources\views",
    r"c:\xampp\htdocs\myfirstlaravel"
]

files_to_check = []
for d in directories:
    for root, _, files in os.walk(d):
        for f in files:
            if f.endswith('.blade.php') or f == '.env' or f == 'app.blade.php':
                files_to_check.append(os.path.join(root, f))

for file_path in files_to_check:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace ResolvedIT, ResolvedIt, Resolvedit with Resolve
        new_content = re.sub(r'ResolvedIT', 'Resolve', content, flags=re.IGNORECASE)
        
        if new_content != content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Updated {file_path}")
    except Exception as e:
        print(f"Error on {file_path}: {e}")
