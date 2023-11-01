<?php

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CommentMessageHandler
{
    public function __construct(
        private CommentRepository $commentRepository,
        private SpamChecker $spamChecker,
        private EntityManagerInterface $entityManager
    ){}

    public function __invoke(CommentMessage $commentMessage): void
    {
        $comment = $this->commentRepository->find($commentMessage->getId());

        if(!$comment){
            return;
        }

        if($this->spamChecker->getSpamScore($comment, $commentMessage->getContext()) === 2){
            $comment->setState('spam');
        }else{
            $comment->setState('published');
        }

        $this->entityManager->flush();
    }
}