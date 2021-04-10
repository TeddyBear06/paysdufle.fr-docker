from waitress import serve
import tts

serve(tts.app, host='0.0.0.0', port=8080, url_scheme='https')