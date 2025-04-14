<?php

namespace Concrete\Package\VisualTips\Block\VisualTips;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\ErrorList\ErrorList;

class Controller extends BlockController
{
    protected $btTable = 'btVisualTips';
    protected $btInterfaceWidth = "800";
    protected $btInterfaceHeight = "700";
    protected $btCacheBlockOutputLifetime = 300;

    public function getBlockTypeDescription(): string
    {
        return t('Add interactive tooltips to any image with pinpoint accuracy.');
    }

    public function getBlockTypeName(): string
    {
        return t("Visual Tips");
    }

    public function registerViewAssets($outputContent = '')
    {
        parent::registerViewAssets($outputContent);

        $this->requireAsset("javascript", "bootstrap");
    }

    public function view()
    {
        /** @var Connection $db */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);
        /** @noinspection PhpDeprecationInspection */
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        $this->set("items", $db->fetchAll("SELECT * FROM btVisualTipsItems WHERE bID = ?", [$this->bID]));
    }

    public function add()
    {
        $this->set("items", []);
        $this->set("fID", null);
    }

    public function edit()
    {
        /** @var Connection $db */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);
        /** @noinspection PhpDeprecationInspection */
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        $this->set("items", $db->fetchAll("SELECT * FROM btVisualTipsItems WHERE bID = ?", [$this->bID]));
    }

    public function delete()
    {
        /** @var Connection $db */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db->executeQuery("DELETE FROM btVisualTipsItems WHERE bID = ?", [$this->bID]);

        parent::delete();
    }

    public function save($args)
    {
        parent::save($args);

        /** @var Connection $db */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        $db->executeQuery("DELETE FROM btVisualTipsItems WHERE bID = ?", [$this->bID]);

        if (is_array($args["items"])) {
            foreach ($args["items"] as $item) {
                /** @noinspection PhpUnhandledExceptionInspection */
                /** @noinspection SqlDialectInspection */
                /** @noinspection SqlNoDataSourceInspection */
                $db->executeQuery("INSERT INTO btVisualTipsItems (bID, x, y, body) VALUES (?, ?, ?, ?)", [
                    $this->bID,
                    isset($item["x"]) && !empty($item["x"]) ? (int)$item["x"] : null,
                    isset($item["y"]) && !empty($item["y"]) ? (int)$item["y"] : null,
                    isset($item["body"]) && !empty($item["body"]) ? $item["body"] : null
                ]);
            }
        }
    }

    public function validate($args): ErrorList
    {
        $e = new ErrorList;

        if (empty($args["fID"])) {
            $e->addError("You need to enter a valid image.");
        }

        if (isset($args["items"])) {
            foreach ($args["items"] as $item) {
                if (empty($item["x"])) {
                    $e->addError("You need to select a valid X coordinate.");
                }

                if (empty($item["y"])) {
                    $e->addError("You need to select a valid Y coordinate.");
                }

                if (empty($item["y"])) {
                    $e->addError("You need to select a valid body.");
                }
            }
        } else {
            $e->addError("You need to add at least one item.");
        }

        return $e;
    }

    public function duplicate($newBID)
    {
        parent::duplicate($newBID);

        /** @var Connection $db */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);

        $copyFields = 'x, y, body';

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpDeprecationInspection */
        /** @noinspection SqlNoDataSourceInspection */
        /** @noinspection PhpArgumentWithoutNamedIdentifierInspection */
        $db->executeUpdate("INSERT INTO btVisualTipsItems (bID, $copyFields) SELECT ?, $copyFields FROM btVisualTipsItems WHERE bID = ?", [
                $newBID,
                $this->bID
            ]
        );
    }
}