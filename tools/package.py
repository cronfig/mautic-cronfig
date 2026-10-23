#!/usr/bin/env python3
"""Build a reproducible plugin-only ZIP without credentials or application data."""
import argparse
import hashlib
import json
from pathlib import Path
import re
import zipfile

root = Path(__file__).resolve().parents[1]
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('--output', type=Path, required=True)
args = parser.parse_args()
version = json.loads((root / 'composer.json').read_text())['version']
config_version = re.search(r"'version'\s*=>\s*'([^']+)'", (root / 'Config/config.php').read_text()).group(1)
if version != config_version:
    raise SystemExit('Composer and plugin versions differ')
files = [root / name for name in ('composer.json', 'CronfigBundle.php', 'LICENSE', 'README.md', 'CHANGELOG.md')]
for directory in ('Assets', 'Config', 'Controller', 'Model', 'Resources', 'Translations'):
    files.extend(p for p in (root / directory).rglob('*') if p.is_file() and not any(part.startswith('.') for part in p.relative_to(root).parts))
args.output.parent.mkdir(parents=True, exist_ok=True)
with zipfile.ZipFile(args.output, 'w', compression=zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(files):
        if path.is_symlink():
            raise SystemExit('Refusing symlink: ' + str(path))
        entry = zipfile.ZipInfo('CronfigBundle/' + path.relative_to(root).as_posix(), date_time=(2020, 1, 1, 0, 0, 0))
        entry.external_attr = 0o100644 << 16
        entry.compress_type = zipfile.ZIP_DEFLATED
        archive.writestr(entry, path.read_bytes())
with zipfile.ZipFile(args.output) as archive:
    if archive.testzip() is not None:
        raise SystemExit('ZIP integrity check failed')
digest = hashlib.sha256(args.output.read_bytes()).hexdigest()
args.output.with_suffix(args.output.suffix + '.sha256').write_text(f'{digest}  {args.output.name}\n')
print(f'Prepared {args.output} ({len(files)} files), SHA256 {digest}')
