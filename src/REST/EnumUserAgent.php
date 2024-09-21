<?php
namespace Krishna\Utilities\REST;

enum EnumUserAgent: string {
	case Krishna = 'Krishna/REST/Fetcher';
	case Browser = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';
	case Both = 'Krishna/REST/Fetcher Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';
}