<?php
namespace PHPWebCore;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Controller
{
	protected ServerRequestInterface $request;
	protected ResponseInterface $response;
	protected ?string $view = null;
	protected $bucket = null;

	/**
     * Return an instance with the specified request.
     *
     * @param ServerRequestInterface $request
     * @return static
     */
	public function withRequest(ServerRequestInterface $request)
	{
		$clone = clone $this;
		$clone->request = $request;
		return $clone;
	}

	/**
     * Return an instance with the specified response.
     *
     * @param ResponseInterface $response
     * @return static
     */
	public function withResponse(ResponseInterface $response)
	{
		$clone = clone $this;
		$clone->response = $response;
		return $clone;
	}

	/**
     * Return an instance with the specified bucket.
     *
     * @param mixed $bucket
     * @return static
     */
	public function withBucket($bucket)
	{
		$clone = clone $this;
		$clone->bucket = $bucket;
		return $clone;
	}

	/**
     * Return an instance with the specified view filename (path included).
     *
     * @param string $view
     * @return static
     */
	public function withView(string $view)
	{
		$clone = clone $this;
		$clone->view = "/".ltrim(ltrim($view, "/"), "\\");
		return $clone;
	}

	/**
     * Include view file.
     *
     * @param array $args arguments sent to view
     */
	public function view(array $args = [])
	{
		if (isset($this->view))
		{
			$file = App::getAppFolder().$this->view;
			if (file_exists($file))
				include_once($file);
			else if (file_exists($file.".php"))
				include_once($file.".php");
			else if (file_exists($file.".html"))
				include_once($file.".html");
			else
				throw new NotFoundException("View {$this->view} not found", 404);
		}
	}

	/**
     * Write response to response header.
     */
	public function applyResponse()
	{
		header(Initialization::getProtocol()." {$this->response->getStatusCode()} {$this->response->getReasonPhrase()}", true);
	}
}
?>
