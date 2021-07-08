import websockets
import asyncio
import json

USERS = set()

def users_event():
    return json.dumps({"count": len(USERS)})

async def notify_users():
    if USERS:
        message = users_event()
        await asyncio.wait([user.send(message) for user in USERS])

async def register(websocket):
    USERS.add(websocket)
    await notify_users()

async def unregister(websocket):
    USERS.remove(websocket)
    await notify_users()

async def counter(websocket, path):
    await register(websocket)
    try:
        await websocket.send(state_event())
        async for message in websocket:
            # Nothing to do here
    finally:
        await unregister(websocket)

start_server = websockets.serve(counter, '0.0.0.0', 8090)

asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()