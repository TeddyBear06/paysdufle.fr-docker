from flask import Flask
import redis

app = Flask(__name__)
r = redis.Redis(host='localhost', port=6379, db=0)
p = r.pubsub()

@app.route("/<random_id>")
def register():
    