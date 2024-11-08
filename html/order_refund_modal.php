<?php
if (isset($order)) {
?>
    <div class="modal fade" id="refundModal_<?php echo esc_attr($post_id); ?>" tabindex="-1" aria-labelledby="refundModalLabel_<?php echo esc_attr($post_id); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel_<?php echo esc_attr($post_id); ?>">Confirm Refund</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 1.25rem;">&times;</span>
                    </button>
                </div>
                <div class="container mt-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Order Details</h4>
                        </div>
                        <div class="card-body fs-4">
                            <h5 class="card-title ">Order ID: <?php echo $order->get_id(); ?></h5>
                            <p><strong>Total:</strong> $<?php echo $order->get_total(); ?></p>
                            <p><strong>Billing Name:</strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>

                            <h5 class="mt-4">Items</h5>
                            <ul class="list-group">
                                <?php
                                $charge_id = $order->get_transaction_id();
                                if (empty($charge_id)) {
                                    throw new \Exception("Charge Id is missing or undefined.");
                                }
                                \Stripe\Stripe::setApiKey('sk_test_51QIPVsRq3Pbq5XLIKYAjtlCvMtp2VPGvMYFHzwuMFDsV5Acyi5hAvkMorh3oZIDHzGAgasHcVJuGBhXDZAYRMykA00Db32EDMH');
                                $charge = \Stripe\Charge::retrieve($charge_id);

                                $balanceTransactionId = $charge->balance_transaction;

                                if (empty($balanceTransactionId)) {
                                    throw new \Exception("balanceTransactionId is missing or undefined.");
                                }

                                $balanceTransaction = \Stripe\BalanceTransaction::retrieve($balanceTransactionId);
                                $stripeFee = $balanceTransaction->fee / 100;
                                $payoutAmount = $balanceTransaction->net / 100;
                                ?>
                                <?php foreach ($order->get_items() as $item) {
                                    $product = $item->get_product();
                                    $product_image = wp_get_attachment_image_src($product->get_image_id(), 'thumbnail');
                                ?>
                                    <li class="list-group-item d-flex align-items-center">
                                        <?php if ($product_image) { ?>
                                            <img src="<?php echo esc_url($product_image[0]); ?>" alt="<?php echo esc_attr($item->get_name()); ?>" class="img-thumbnail mr-3" style="width: 60px; height: 60px;">
                                        <?php } ?>
                                        <div>
                                            <strong>Item ID:</strong> <?php echo $item->get_id(); ?><br>
                                            <strong>Product Name:</strong> <?php echo $item->get_name(); ?><br>
                                            <strong>Quantity:</strong> <?php echo $item->get_quantity(); ?><br>
                                            <strong>Price:</strong> <?php echo wc_price($item->get_total()); ?>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-body fs-5">
                    Are you sure you want to refund this order for <?php echo wc_price($amount); ?>?
                    <br>
                    <div class="tip"> ?
                        <span class="tooltiptext">This represent the fee Stripe collects for the transaction.</span>
                    </div><?php echo "<strong>Stripe Fee : </strong>"  . wc_price(($stripeFee * 84.37), array('decimals' => 3)); ?>
                    <br>
                    <div class="tip2"> ?
                        <span class="tooltiptext2">This represent the net total that will be credited to your Stripe bank account.</span>
                    </div><?php echo "<strong> Stripe Payout : </strong> " . wc_price(($payoutAmount * 84.37), array('decimals' => 3)); ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger refund-button" data-order-id="<?php echo $order->get_id() ?>">Confirm Refund</button>
                </div>
            </div>
        </div>
    </div>

<?php
}
?>