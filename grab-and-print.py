#! /usr/bin/env python3

import base64
import time
import subprocess
from pathlib import Path

import requests

URL = 'https://nac.aoe2.se/nac5'
INTERVAL_SECONDS = 10
SECRET = '0000000000111111111122222222223333333333444444444455555555556666'


def main():
    while True:
        try:
            result = requests.post(f'{URL}/api.php?getpdfs', json={'secret': SECRET})
            filelist = result.json()['files']

            for filename in filelist:
                print(filename)
                target_file = Path('/tmp') / f'{filename}.pdf'
                result = requests.get(f'{URL}/data/{filename}')
                target_file.write_bytes(base64.b64decode(result.content))
                run_result = subprocess.run(['identify', target_file], capture_output=True, text=True)
                if '288x180' in run_result.stdout:
                    subprocess.run(['lpr', target_file])
                    requests.post(f'{URL}/api.php?printed', json={'secret': SECRET})
                else:
                    print(f'Invalid format: {run_result.stdout}')
                requests.post(f'{URL}/api.php?delete', json={'secret': SECRET, 'token': filename})
            time.sleep(INTERVAL_SECONDS)
        except Exception:
            pass


if __name__ == '__main__':
    main()
