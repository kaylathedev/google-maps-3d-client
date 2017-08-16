import re
import sys

filename_in  = sys.argv[1]
filename_out = sys.argv[2]

with open(filename_in, 'rb') as file:
  contents = file.read()

m = re.findall(b'\\\\([0-8]{3})', contents)
for match in m:
  number = int(match, 8)
  regex = b'\\\\' + match
  sub = (chr(number)).encode('utf-8')
  contents = re.sub(regex, sub, contents)

contents = re.sub(br'\\\\', b"\\\\", contents)
contents = re.sub(br'\\\'', b"\'", contents)
contents = re.sub(br'\\\"', b"\"", contents)
contents = re.sub(br'\\r', b"\r", contents)
contents = re.sub(br'\\n', b"\n", contents)
contents = re.sub(br'\\t', b"\t", contents)

with open(filename_out, 'wb') as file:
  file.write(contents)
