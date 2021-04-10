from flask import Flask, make_response
from gtts import gTTS
from markupsafe import escape
from os import path
import hashlib
import os
import urllib.parse

app = Flask(__name__)

@app.route('/<word>')
def word(word):
    escaped_word = urllib.parse.unquote(word)
    mp3_folder = os.path.dirname(os.path.abspath(__file__)) + '/mp3/'
    if escaped_word != 'favicon.ico':
        m = hashlib.md5()
        m.update(str.encode(escaped_word))
        filename = str(m.hexdigest()) + '.mp3'
        if not path.exists(mp3_folder + filename):
            tts = gTTS(escaped_word, lang='fr')
            tts.save(mp3_folder + filename)
        response = make_response(filename)
        if os.environ['APP_ENV'] == 'local':
            response.headers['Access-Control-Allow-Origin'] = 'https://localhost'
        else:
            response.headers['Access-Control-Allow-Origin'] = 'https://paysdufle.fr'
        return response
    else:
        return ''

if __name__ == "__main__":
    app.run(host='0.0.0.0')