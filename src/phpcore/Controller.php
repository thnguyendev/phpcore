<?php
namespace PHPCore;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Controller
{
	protected $request;
	protected $response;
	protected $view = null;
	protected $bucket = [];

	public function withRequest(ServerRequestInterface $request)
	{
		$clone = clone $this;
		$clone->request = $request;
		return $clone;
	}

	public function withResponse(ResponseInterface $response)
	{
		$clone = clone $this;
		$clone->response = $response;
		return $clone;
	}

	public function withBucket(array $bucket)
	{
		$clone = clone $this;
		$clone->bucket = $bucket;
		return $clone;
	}

	public function withView(string $view)
	{
		$clone = clone $this;
		$clone->view = "/".ltrim(ltrim($view, "/"), "\\");
		return $clone;
	}

	public function view(array $args = [])
	{
		if (isset($this->view))
		{
			$file = App::getAppFolder().$this->view;
			if (file_exists($file))
				require_once($file);
			else
			{
				$file .= ".php";
				if (file_exists($file))
					require_once($file);
				else
					throw new NotFoundException("View {$this->view} not found", 404);
			} 

		}
	}

	public function applyResponse()
	{
		header(Initialization::getProtocol()." {$this->response->getStatusCode()} {$this->response->getReasonPhrase()}", true);
	}
}
?>
