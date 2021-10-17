<?php
namespace PHPWebCore;

class ContentType extends Enum
{
    const ApplicationJson = "Content-Type: application/json";
    const ApplicationXml = "Content-Type: application/xml";
    const ApplicationXForm = "Content-Type: application/x-www-form-urlencoded";
    const TextPlain = "Content-Type: text/plain";
    const TextCsv = "Content-Type: text/csv";
    const TextHtml = "Content-Type: text/html";
    const TextXml = "Content-Type: text/xml";
    const MultipartFormData = "Content-Type: multipart/form-data";
}
?>