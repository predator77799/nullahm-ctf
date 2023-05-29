package main

import (
	"context"
	"database/sql"
	"log"
	"net"

	pb "nullahm-ctf/pb"
	_ "github.com/mattn/go-sqlite3"
	"google.golang.org/grpc"
)

type server struct {
	pb.UnimplementedLoginServiceServer
}

func (s *server) LoginUser(ctx context.Context, in *pb.LoginRequest) (*pb.LoginResponse, error) {
	db, err := sql.Open("sqlite3", "./users.db")
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer db.Close()

	var count int
	err = db.QueryRow("SELECT COUNT(*) FROM users WHERE username = '" + in.Username + "' AND password = '" + in.Password + "'").Scan(&count)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return &pb.LoginResponse{Success: count > 0}, nil
}

func main() {
	lis, err := net.Listen("tcp", ":50001")
	if err != nil {
		log.Fatalf("failed to listen: %v", err)
	}
	s := grpc.NewServer()
	pb.RegisterLoginServiceServer(s, &server{})
	if err := s.Serve(lis); err != nil {
		log.Fatalf("failed to serve: %v", err)
	}
}

