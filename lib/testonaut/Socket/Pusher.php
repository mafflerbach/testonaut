<?php

namespace testonaut\Socket;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class Pusher implements \Ratchet\Wamp\WampServerInterface {
  /**
   * A lookup of all the topics clients have subscribed to
   */
  protected $subscribedTopics = array();

  public function onSubscribe(ConnectionInterface $conn, $topic) {
    $this->subscribedTopics[$topic->getId()] = $topic;
  }

  /**
   * @param string JSON'ified string we'll receive from ZeroMQ
   */
  public function onBlogEntry($entry) {
    $entryData = json_decode($entry, true);

    var_dump($this->subscribedTopics);
    // If the lookup topic object isn't set there is no one to publish to
    if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
      print('sdf');
      return;
    }

    $topic = $this->subscribedTopics[$entryData['category']];

    // re-send thedata to all the clients subscribed to that category
    $topic->broadcast($entryData);
  }

  /**
   * When a new connection is opened it will be passed to this method
   * @param  ConnectionInterface $conn The socket/connection that just connected to your application
   * @throws \Exception
   */
  function onOpen(ConnectionInterface $conn) {
    // TODO: Implement onOpen() method.
  }

  /**
   * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
   * @param  ConnectionInterface $conn The socket/connection that is closing/closed
   * @throws \Exception
   */
  function onClose(ConnectionInterface $conn) {
    // TODO: Implement onClose() method.
  }

  /**
   * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
   * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
   * @param  ConnectionInterface $conn
   * @param  \Exception $e
   * @throws \Exception
   */
  function onError(ConnectionInterface $conn, \Exception $e) {
    // TODO: Implement onError() method.
  }

  /**
   * An RPC call has been received
   * @param \Ratchet\ConnectionInterface $conn
   * @param string $id The unique ID of the RPC, required to respond to
   * @param string|Topic $topic The topic to execute the call against
   * @param array $params Call parameters received from the client
   */
  function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
    // TODO: Implement onCall() method.
  }

  /**
   * A request to unsubscribe from a topic has been made
   * @param \Ratchet\ConnectionInterface $conn
   * @param string|Topic $topic The topic to unsubscribe from
   */
  function onUnSubscribe(ConnectionInterface $conn, $topic) {
    // TODO: Implement onUnSubscribe() method.
  }

  /**
   * A client is attempting to publish content to a subscribed connections on a URI
   * @param \Ratchet\ConnectionInterface $conn
   * @param string|Topic $topic The topic the user has attempted to publish to
   * @param string $event Payload of the publish
   * @param array $exclude A list of session IDs the message should be excluded from (blacklist)
   * @param array $eligible A list of session Ids the message should be send to (whitelist)
   */
  function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
    // TODO: Implement onPublish() method.
  }

  /* The rest of our methods were as they were, omitted from docs to save space */
}